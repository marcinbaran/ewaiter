<div class="flex flex-col gap-2 {{ $containerClass !== '' ? $containerClass : '' }}">
    @if ($type == 'textarea')
        <x-admin.form.input.textarea :attributes="$attributes" :name="$name" :id="$id" :value="$value"
                                     :disabled="$disabled" :required="$required" :readonly="$readonly"
                                     :placeholder="$placeholder" :min="$min" :max="$max"
                                     :rows="$rows" :error="$error" :class="$class" />
    @elseif($type == 'email')
        <x-admin.form.input.email :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                  :value="$value" :min="$min" :max="$max" :placeholder="$placeholder"
                                  :required="$required"
                                  :readonly="$readonly" :disabled="$disabled" :error="$error" :showIcon="$showIcon" />
    @elseif($type == 'phone')
        <x-admin.form.input.phone :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                  :value="$value" :placeholder="$placeholder" :required="$required"
                                  :readonly="$readonly" :disabled="$disabled"
                                  :error="$error" :showIcon="$showIcon" />
    @elseif($type == 'postal-code')
        <x-admin.form.input.postal-code :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                        :value="$value" :placeholder="$placeholder" :required="$required"
                                        :readonly="$readonly" :disabled="$disabled"
                                        :error="$error" :showIcon="$showIcon" />
    @elseif($type == 'bank-account')
        <x-admin.form.input.bank-account :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                         :value="$value" :placeholder="$placeholder" :required="$required"
                                         :readonly="$readonly" :disabled="$disabled"
                                         :error="$error" :showIcon="$showIcon" />
    @elseif($type == 'money')
        <x-admin.form.input.money :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                  :value="$value" :min="$min" :max="$max" :placeholder="$placeholder"
                                  :required="$required" :readonly="$readonly"
                                  :disabled="$disabled" :error="$error" :showIcon="$showIcon" />
    @elseif($type == 'percent')
        <x-admin.form.input.percent :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                    :value="$value" :min="$min" :max="$max" :step="$step" :placeholder="$placeholder"
                                    :required="$required" :readonly="$readonly" :disabled="$disabled" :error="$error"
                                    :showIcon="$showIcon" />
    @elseif($type == 'time')
        <x-admin.form.input.time :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                 :value="$value" :min="$min" :max="$max" :placeholder="$placeholder"
                                 :required="$required"
                                 :readonly="$readonly" :disabled="$disabled" :error="$error" :showIcon="$showIcon" />
    @elseif($type == 'date')
        <x-admin.form.input.date :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                 :value="$value" :min="$min" :max="$max" :placeholder="$placeholder"
                                 :required="$required"
                                 :readonly="$readonly" :disabled="$disabled" :error="$error" :showIcon="$showIcon" />
    @elseif($type == 'date-time')
        <x-admin.form.input.date-time :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                      :value="$value" :min="$min" :max="$max" :minTime="$minTime"
                                      :placeholder="$placeholder"
                                      :required="$required"
                                      :readonly="$readonly" :disabled="$disabled" :error="$error"
                                      :showIcon="$showIcon" :step="$step" />
    @elseif($type == 'password')
        <x-admin.form.input.password :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                     :value="$value" :min="$min" :max="$max" :placeholder="$placeholder"
                                     :required="$required" :maxlength="$max" :minlength="$min"
                                     :readonly="$readonly" :disabled="$disabled" :error="$error" />
    @elseif($type == 'number')
        <x-admin.form.input.number :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                   :value="$value" :min="$min" :max="$max" :step="$step" :placeholder="$placeholder"
                                   :required="$required"
                                   :readonly="$readonly" :disabled="$disabled" :error="$error" :prefix="$prefix"
                                   :suffix="$suffix" />
    @elseif($type == 'text')
        <x-admin.form.input.text :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                 :value="$value" :min="$min" :max="$max" :placeholder="$placeholder"
                                 :required="$required"
                                 :readonly="$readonly" :disabled="$disabled" :error="$error" :prefix="$prefix"
                                 :suffix="$suffix" />
    @elseif($type == 'toggle')
        <x-admin.form.input.toggle :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                   :value="$value" :uncheckedValue="$uncheckedValue" :checked="$checked"
                                   :placeholder="$placeholder" :required="$required" :disabled="$disabled"
                                   :error="$error" />
    @elseif($type == 'select')
        <x-admin.form.input.select :attributes="$attributes" :name="$name" :id="$id" :class="$class" :value="$value"
                                   :oldValue="$oldValue" :mode="$mode" :placeholder="$placeholder" :required="$required"
                                   :disabled="$disabled" :error="$error" :nullOption="$nullOption">
            {{ $slot }}
        </x-admin.form.input.select>
    @elseif($type == 'text-select')
        <x-admin.form.input.text-select :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                        :value="$value" :min="$min" :max="$max" :placeholder="$placeholder"
                                        :required="$required" :readonly="$readonly" :disabled="$disabled"
                                        :error="$error">
            {{ $slot }}
        </x-admin.form.input.text-select>
    @elseif($type == 'number-select')
        <x-admin.form.input.number-select :attributes="$attributes" :name="$name" :id="$id" :class="$class"
                                          :value="$value" :min="$min" :max="$max" :step="$step"
                                          :placeholder="$placeholder" :required="$required" :readonly="$readonly"
                                          :disabled="$disabled" :error="$error">
            {{ $slot }}
        </x-admin.form.input.number-select>
    @endif
    @if ($error)
        <p class="text-red-600">{{ $error }}</p>
    @endif
</div>
