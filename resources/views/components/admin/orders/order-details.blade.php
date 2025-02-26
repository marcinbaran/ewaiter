@props(['orders' => [], 'class' => ''])

<div class="{{ $class }} flex flex-col gap-2">
    <h3 class="font-bold">{{ __('admin.Ordered items') }}</h3>
    <ol class="list-decimal flex-grow text-sm pl-4">
        @if($orders)
            @foreach ($orders[0]->orders as $row)
                <li class="pb-2">
                    <div class="flex flex-col items-start">
                    <span class="font-bold break-all"> {{ $row->dish->name ?? __('admin.Dish removed') }}
                        x{{ $row->quantity }} - {!!(new \App\Decorators\MoneyDecorator())->decorate($row->price * $row->quantity,  'PLN') !!}</span>
                        @if (count($row->getAdditions()))
                            @foreach ($row->getAdditions() as $addition)
                                <span class="text-xs text-gray-600 dark:text-gray-400 break-all">
                                {{ $addition->name }}, {!! (new \App\Decorators\MoneyDecorator())->decorate($addition->price, 'PLN') !!}, {{ $addition->quantity }} szt.
                            </span>
                            @endforeach
                        @endif
                    </div>
                </li>
            @endforeach
        @endif
    </ol>

</div>
