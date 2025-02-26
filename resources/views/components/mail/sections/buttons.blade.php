@props(['buttons' => []])

<mj-section padding-top="0">
    @foreach($buttons as $button)
        <mj-column>
            <x-mail.common.button :color="$button['color']" :text="$button['text']" :url="$button['url']" />
        </mj-column>
    @endforeach
</mj-section>
