@props(['user_name', 'description'])

<mj-section>
    <mj-column>
        <x-mail.common.heading-2>
            {{$user_name}},
        </x-mail.common.heading-2>
        <mj-text align="justify">
            {{$description}}
        </mj-text>
    </mj-column>
</mj-section>
