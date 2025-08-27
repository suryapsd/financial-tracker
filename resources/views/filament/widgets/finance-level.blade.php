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
            $gradients = ['from-yellow-100/20 to-yellow-200/20', 'from-green-100/20 to-green-200/20', 'from-blue-100/20 to-blue-200/20', 'from-purple-100/20 to-purple-200/20', 'from-pink-100/20 to-pink-200/20', 'from-orange-100/20 to-orange-200/20'];
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
