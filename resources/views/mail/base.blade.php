@props([
    'website_title' => 'E-Waiter',
    'title',
    'user_name',
    'description',
    'orders' => [],
    'prices' => [],
    'buttons' => [],
    'greeting' => null
])

<x-mail.layout :website_title="$website_title" :title="$title" :user_name="$user_name" :description="$description"
               :greeting="$greeting" />
