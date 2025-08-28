<x-filament::widget>
  <x-filament::card class="space-y-4">
    <div class="text-base font-semibold mt-2">Level Kebebasan Finansial</div>
    <div class="text-lg text-gray-600 dark:text-gray-300"><small>Seberapa bebas hidup kamu tanpa ketegantungan pada gaji pekerjaan</small></div>

    <div class="overflow-x-auto mt-5 rounded-lg border">
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
            $gradients = array_reverse(['from-green-100/20 to-green-200/20', 'from-green-200/20 to-green-300/20', 'from-green-300/20 to-green-400/20', 'from-green-400/20 to-green-500/20', 'from-green-500/20 to-green-600/20', 'from-green-600/20 to-green-700/20', 'from-green-700/20 to-green-800/20']);
          @endphp

          @foreach (array_reverse($this->getData()['levels']) as $level)
            @php
              $gradient = $gradients[($loop->iteration - 1) % count($gradients)];
              $rowClass = "bg-gradient-to-r {$gradient} text-gray-800 dark:text-gray-200";
            @endphp
            <tr class="{{ $loop->last ? '' : 'border-b' }} {{ $rowClass }}">
              <td class="px-4 text-center">
                @if ($level['current'])
                  <span class="text-primary-600 dark:text-primary-300 font-semibold">✅</span>
                @endif
              </td>
              <td class="px-4 py-2 text-center">
                Level {{ $level['level'] }}
              </td>
              <td class="px-4 py-2">
                {{ $level['label'] }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="text-sm mt-5 mb-4">Perlu menabung <span class="font-semibold">{{ number_format($this->getData()['naikLevel'], 2) }}</span> lagi untuk naik level</div>
  </x-filament::card>
</x-filament::widget>
