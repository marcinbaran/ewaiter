<div class="flex flex-col h-full w-full justify-between relative overflow-hidden text-gray-500 dark:text-gray-400">
    <div class="w-full overflow-x-auto">
        <table class="min-w-full w-max divide-y divide-gray-200 table-fixed dark:divide-gray-600 mb-4">
            <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
                <th scope="col" class="p-4 text-xs font-medium text-left uppercase">
                    {{ __('bills.order_table.name') }}
                </th>
                <th scope="col" class="p-4 text-xs font-medium text-left uppercase">
                    {{ __('bills.order_table.quantity') }}
                </th>
                <th scope="col" class="p-4 text-xs font-medium text-left uppercase">
                    {{ __('bills.order_table.dish_price') }}
                </th>
                <th scope="col" class="p-4 text-xs font-medium text-left uppercase">
                    {{ __('bills.order_table.additions_price') }}
                </th>
                <th scope="col" class="p-4 text-xs font-medium text-left uppercase">
                    {{ __('bills.order_table.final_price') }}
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
            @php
                $collectivePriceProductsInOrder=0;
            @endphp
            @foreach($data->orders as $row)
                @php
                    $additionSum = 0;
                @endphp
                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                        @if(isset($row->products_in_order))
                            @if($row->type == \App\Enum\OrderType::BUNDLE)
                                <div class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{  "Zestaw " .$row->item_name ?? __('admin.Dish removed') }}
                                </div>
                            @endif
                            @foreach($row->products_in_order as $product)
                                @php
                                    $productAdditionSum = 0;
                                @endphp
                                @if($row->type == \App\Enum\OrderType::BUNDLE)
                                        <div class="flex flex-col gap-1 mt-2 text-sm  text-gray-500 dark:text-gray-400 font-bold">
                                            {{  $product['name'] }}
                                        </div>
                                @else
                                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ $row->{$row->type}->name ?? __('admin.Dish removed') }}
                                    </div>
                                @endif

                                <div class="flex flex-col gap-1 mt-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                    @foreach($row->customize['additions'] as $addition)
                                        @if(isset($addition['dish_id']) && $addition['dish_id'] == $product['id'])
                                            @php
                                                $additionPrice = (float) $addition['price'];
                                                $productAdditionSum += ($additionPrice * $addition['quantity'] );
                                                $additionSum += ($additionPrice * $addition['quantity']);
                                            @endphp
                                            <p>
                                                {{ $addition['quantity'] }}x {{ $addition['name'] }}
                                                - {!! (new \App\Decorators\MoneyDecorator)->decorate($addition['price'], 'PLN') !!}
                                            </p>
                                        @else
                                            <p>
                                                {{ $addition['quantity'] }}x {{ $addition['name'] }}
                                                - {!! (new \App\Decorators\MoneyDecorator)->decorate($addition['price'], 'PLN') !!}
                                            </p>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            @php
                                foreach($row->getAdditions() as $addition) {
                                    $additionSum += ($addition->price * $addition->quantity);
                                }
                            @endphp
                            <div class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $row->dish->name ?? __('admin.Dish removed') }}
                            </div>
                            <div>
                                {{ $row->type == \App\Enum\OrderType::DISH ? ($row->{$row->type}->category->name ?? __('admin.Category removed')) : ($row->{$row->type}->category->name ?? __('admin.bundle')) }}
                            </div>
                            <div class="flex flex-col gap-1 mt-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                    @foreach($row->getAdditions() as $addition)
                                        <p>
                                            {{$addition->quantity }}x {{ $addition->name }}
                                            - {!! (new \App\Decorators\MoneyDecorator)->decorate($addition->price,'PLN') !!}
                                        </p>
                                    @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $row->quantity }}
                    </td>
                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {!! (new \App\Decorators\DiscountedPriceDecorator)->decorate($row->price, $row->discount) !!}
                    </td>
                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ (new \App\Decorators\MoneyDecorator)->decorate($additionSum) }}
                    </td>
                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        @php
                        $productPrice=($row->price * $row->quantity)+$additionSum;
                        $collectivePriceProductsInOrder+=$productPrice;
                        @endphp
                        {!! (new \App\Decorators\MoneyDecorator)->decorate($productPrice) !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="w-full h-auto pb-4">
        <h3 class="mb-4 text-xl  font-semibold text-gray-900 dark:text-gray-50">{{__('admin.Summary')}}</h3>
        @include('admin.bills.partials.summary')
    </div>
</div>
