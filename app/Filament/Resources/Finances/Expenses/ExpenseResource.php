<?php

namespace App\Filament\Resources\Finances\Expenses;

use BackedEnum;
use Carbon\Carbon;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Category;
use Filament\Tables\Table;
use App\Services\OCRService;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use App\Services\GeminiAIService;
use Illuminate\Http\UploadedFile;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Resources\Finances\Expenses\Pages\ManageExpenses;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowLeftStartOnRectangle;
    protected static string | \UnitEnum | null $navigationGroup = 'Finances';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Expenses Information')
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([
                                self::expenseDetailsSection(),
                            ]),
                        Tab::make('Receipt & OCR')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                self::receiptUploadSection(),
                                self::parsedDataSection(),
                            ]),
                    ])
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name')
                    ->label('Account'),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->formatStateUsing(function ($state, $record) {
                        $color = $record->category->color ?? '#999999';
                        return "<span style='
                            display: inline-flex;
                            align-items: center;
                            border-radius: 0.375rem;
                            background-color: {$color}0D;
                            color: {$color};
                            filter: brightness(80%);
                            padding: 0 0.5rem;
                            font-size: 0.75rem;
                            font-weight: 500;
                            border: 1px solid {$color}33;
                        '>{$state}</span>";
                    })
                    ->html(),
                TextColumn::make('name')
                    ->label('Expense')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('IDR', true)
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('expense_date')
                    ->date()
                    ->label('Date'),
                TextColumn::make('frequency')
                    ->label('Frequency'),
            ])
            ->filters([
                SelectFilter::make('category_id')->relationship('category', 'name')->label('Category'),
                SelectFilter::make('frequency')->options([
                    'once' => 'Once',
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly' => 'Yearly',
                ]),
                Filter::make('expense_date')
                    ->label('Tanggal')
                    ->schema([
                        TextInput::make('month')
                            ->type('month')
                    ])
                    ->query(function ($query, array $data) {
                        if (isset($data['month']) && $data['month']) {
                            [$year, $month] = explode('-', $data['month']);
                            return $query->whereYear('expense_date', $year)
                                ->whereMonth('expense_date', $month);
                        }
                        return $query;
                    }),
            ])
            ->defaultSort('expense_date', 'desc')
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(function (array $data, $record): array {
                        // Simpan amount lama untuk perhitungan selisih
                        $record->old_amount = $record->amount;
                        return $data;
                    })
                    ->after(function (EditAction $action, $record) {
                        $account = $record->account;

                        if ($account && isset($record->old_amount)) {
                            $difference = $record->amount - $record->old_amount;

                            $account->update([
                                'balance' => $account->balance + $difference,
                            ]);
                        }
                    }),

                DeleteAction::make()
                    ->before(function ($record) {
                        $account = $record->account;

                        if ($account) {
                            $account->update([
                                'balance' => $account->balance + $record->amount,
                            ]);
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExpenses::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    protected static function expenseDetailsSection(): Grid
    {
        // Section::make('Expense Details')
        //     ->description('Enter your expense details manually or via receipt processing.')
        //     ->schema([]);
        return Grid::make(2)
            ->schema([
                Select::make('category_id')
                    ->required()
                    ->label('Category')
                    ->options(Category::where('type', 'expense')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select a category'),

                Select::make('account_id')
                    ->required()
                    ->label('Account')
                    ->options(Account::where('user_id', Auth::id())->where('is_active', 1)->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select an account'),

                TextInput::make('name')
                    ->required()
                    ->label('Expense Name')
                    ->placeholder('e.g. Electricity, Rent, Food, etc.')
                    ->maxLength(255),

                TextInput::make('amount')
                    ->required()
                    ->label('Amount')
                    ->placeholder('Enter the total amount spent')
                    ->prefix('Rp.')
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('expense_date')
                    ->required()
                    ->label('Expense Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(Carbon::today())
                    ->closeOnDateSelection(),

                Select::make('frequency')
                    ->label('Frequency')
                    ->placeholder('Select frequency')
                    ->default('once')
                    ->options([
                        'once' => 'Once',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ]),

                Textarea::make('description')
                    ->label('Additional Notes')
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    protected static function receiptUploadSection(): Section
    {
        return Section::make('Upload Receipt & OCR Processing')
            ->description('Upload a receipt image to automatically extract expense data.')
            ->columns(2)
            ->schema([
                FileUpload::make('image_struk')
                    ->label('Upload Receipt Image')
                    ->disk('public')
                    ->directory('uploads')
                    ->image()
                    ->imageEditor()
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull()
                    ->afterStateUpdated(function ($state, $set) {
                        if (!$state) return;

                        $filename = uniqid() . '.' . strtolower($state->getClientOriginalExtension());
                        $storedPath = $state->storeAs('uploads', $filename, 'public');
                        $absolutePath = storage_path('app/public/' . $storedPath);

                        self::processOcrAndAutofill($absolutePath, $set);
                    }),

                Textarea::make('raw_text')
                    ->label('Raw OCR Text')
                    ->readOnly()
                    ->rows(5),

                Textarea::make('parsed_json')
                    ->label('Parsed JSON Output')
                    ->readOnly()
                    ->rows(5),
            ])
            ->footerActions([
                Action::make('Manual OCR Process')
                    ->label('Run OCR Now')
                    ->button()
                    ->color('primary')
                    ->action(function ($state, $set, $get) {
                        $uploads = $get('image_struk');
                        if (!$uploads) {
                            Notification::make()->title('No receipt uploaded.')->danger()->send();
                            return;
                        }

                        $uploaded = reset($uploads);
                        if ($uploaded instanceof UploadedFile) {
                            $extension = $uploaded->getClientOriginalExtension();
                            $filename = uniqid() . '.' . strtolower($extension);
                            $storedPath = $uploaded->storeAs('uploads', $filename, 'public');
                        } else {
                            $storedPath = $uploaded;
                        }
                        $absolutePath = storage_path('app/public/' . $storedPath);

                        self::processOcrAndAutofill($absolutePath, $set);

                        Notification::make()->title('OCR processed successfully.')->success()->send();
                    }),
            ]);
    }

    protected static function parsedDataSection(): Section
    {
        return Section::make('AI Parsed Data')
            ->description('This section will be auto-filled if receipt parsing succeeds.')
            ->schema([
                TextInput::make('merchant_name')
                    ->label('Merchant Name')
                    ->placeholder('e.g. BreadTalk, Walmart, etc.'),

                TextInput::make('merchant_address')
                    ->label('Merchant Address')
                    ->placeholder('Address printed on the receipt'),

                Repeater::make('items')
                    ->label('Purchased Items')
                    ->schema([
                        TextInput::make('item_name')->label('Item Name')->required(),

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->default(1)
                            ->afterStateUpdated(function (Set $set, $state, callable $get) {
                                $set('subtotal', $state * ($get('unit_price') ?? 0));
                            }),

                        TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->prefix('Rp.')
                            ->rules(['numeric', 'min:0'])
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->afterStateUpdated(function (Set $set, $state, callable $get) {
                                $set('subtotal', ($get('quantity') ?? 0) * $state);
                            }),

                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->disabled()
                            ->prefix('Rp.')
                            ->rules(['numeric', 'min:0'])
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->afterStateHydrated(function (Set $set, callable $get) {
                                $set('subtotal', ($get('quantity') ?? 0) * ($get('unit_price') ?? 0));
                            }),
                    ])
                    ->defaultItems(0)
                    ->columns(4),
            ]);
    }

    protected static function processOcrAndAutofill(string $imagePath, callable $set): void
    {
        $ocrText = app(OCRService::class)->extractTextFromImage($imagePath);
        $set('raw_text', $ocrText);

        $parsedJson = app(GeminiAIService::class)->parseReceiptToJson($ocrText);
        $set('parsed_json', json_encode($parsedJson, JSON_PRETTY_PRINT));

        // Autofill logic
        if (isset($parsedJson['shop_name'])) $set('merchant_name', $parsedJson['shop_name']);
        if (isset($parsedJson['address'])) $set('merchant_address', $parsedJson['address']);
        if (isset($parsedJson['total_amount'])) $set('amount', $parsedJson['total_amount']);
        if (isset($parsedJson['date'])) $set('expense_date', $parsedJson['date']);
        if (isset($parsedJson['items'])) {
            $items = collect($parsedJson['items'])->map(fn($item) => [
                'item_name' => $item['name'] ?? '',
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'subtotal' => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0),
            ])->toArray();

            $set('items', $items);
        }
    }
}
