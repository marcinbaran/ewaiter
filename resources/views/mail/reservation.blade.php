@props([
    'website_title' => 'E-Waiter',
    'title',
    'user_name',
    'description',
    'orders' => [],
    'prices' => [],
    'buttons' => [],
    'greeting' => null,
    'slot' => null
])

<mjml>
    <mj-head>
        <mj-title>{{$website_title}} - {{$title}}</mj-title>
        <mj-font name="Poppins"
                 href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900" />
        <mj-attributes>
            <mj-all font-family="Poppins, Helvetica" color="#262828" font-size="12px" line-height="150%" />
            <mj-class name="heading-1" font-size="20px" font-weight="700" />
            <mj-class name="heading-2" font-size="16px" font-weight="500" />
            <mj-class name="heading-3" font-size="14px" font-weight="500" />
            <mj-class name="button" font-size="14px" font-weight="500" inner-padding="5px 10px" paddingh />

            <mj-class name="text-white" color="#f5f5f5" />
            <mj-class name="bg-white" background-color="#f5f5f5" />
            <mj-class name="border-px-solid-white" border="1px solid #f5f5f5" />

            <mj-class name="text-black" color="#262828" />
            <mj-class name="bg-black" background-color="#262828" />
            <mj-class name="border-px-solid-black" border="1px solid #262828" />

            <mj-class name="text-light-pink" color="#f1acb7" />
            <mj-class name="bg-light-pink" background-color="#f1acb7" />
            <mj-class name="border-px-solid-light-pink" border="1px solid #f1acb7" />

            <mj-class name="text-pink" color="#ec3f59" />
            <mj-class name="bg-pink" background-color="#ec3f59" />
            <mj-class name="border-px-solid-pink" border="1px solid #ec3f59" />
        </mj-attributes>
    </mj-head>
    <mj-body mj-class="bg-white">
        <mj-spacer height="25px" />
        <mj-wrapper mj-class="border-px-solid-light-pink" padding="0">
            <x-mail.sections.title :title="$title" />
                <mj-section>
                    <mj-column>
                        <x-mail.common.heading-2>
                            {{$user_name}},
                        </x-mail.common.heading-2>
                        <mj-text align="justify">
                            {!! $description !!}
                        </mj-text>
                    </mj-column>
                </mj-section>
            <mj-text>
                {!! $slot !!}
            </mj-text>
            <x-mail.sections.greetings :greeting="$greeting" />
            <x-mail.sections.footer />
        </mj-wrapper>
        <mj-spacer height="25px" />
    </mj-body>
</mjml>
