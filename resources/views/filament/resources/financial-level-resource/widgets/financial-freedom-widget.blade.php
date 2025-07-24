<x-filament::widget>
  <x-filament::card class="space-y-4">
    <div class="text-xl font-bold text-center">Level Kebebasan Finansial</div>
    <p class="text-center"><small>Seberapa bebas hidup kamu tanpa ketegantungan pada gaji pekerjaan</small></p>

    <div class="overflow-x-auto mt-3 mb-4 rounded-lg border">
      <table class="w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm rounded-lg overflow-hidden">
        <thead>
          <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
            <th class="border-b px-4 py-2 text-center">Status</th>
            <th class="border-b px-4 py-2 text-center">Level</th>
            <th class="border-b px-4 py-2 text-center">Label</th>
          </tr>
        </thead>
        <tbody>
          @php
            $gradients = ['from-yellow-100 to-yellow-200', 'from-green-100 to-green-200', 'from-blue-100 to-blue-200', 'from-purple-100 to-purple-200', 'from-pink-100 to-pink-200', 'from-orange-100 to-orange-200'];
          @endphp

          @foreach (array_reverse($this->getData()['levels']) as $level)
            @php
              $gradient = $gradients[($loop->iteration - 1) % count($gradients)];
              $rowClass = $level['current'] ? 'bg-primary-100 dark:bg-primary-900 font-semibold text-primary-700 dark:text-primary-200' : "bg-gradient-to-r {$gradient} text-gray-800 dark:text-gray-200";
            @endphp
            <tr class="{{ $loop->last ? '' : 'border-b' }} {{ $rowClass }}">
              <td class="px-4 text-center">
                @if ($level['current'])
                  <span class="text-primary-600 dark:text-primary-300 font-semibold">✅</span>
                @endif
              </td>
              <td class="px-4 py-2 text-center">
                Level {{ $loop->index }}
              </td>
              <td class="px-4 py-2">
                {{ $level['icon'] }} {{ $level['label'] }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- <x-filament::card>
      <h2>Dana saat ini; </h2>
      <div class="text-sm">Dana saat ini: <span class="font-semibold">@money($this->getData()['sisaDana'])</span></div>
      <div class="text-sm">Pengeluaran per bulan: <span class="font-semibold">@money($this->getData()['perBulan'])</span></div>
      <div class="text-sm">Cukup untuk <span class="font-semibold">{{ $this->getData()['bulan'] }} bulan</span> ke depan</div>
      <div class="text-sm">Bulan bisa hidup tanpa gaji: <span class="font-semibold">{{ number_format($this->getData()['bulanTanpaGaji'], 2) }}</span> bulan</div>
    </x-filament::card> --}}
  </x-filament::card>
</x-filament::widget>
