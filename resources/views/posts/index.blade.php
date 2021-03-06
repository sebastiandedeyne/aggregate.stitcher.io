@php
    /** @var \Domain\Post\Models\Post[] $posts */
    /** @var \Domain\User\Models\User|null $user */
    /** @var \Domain\Post\Models\Tag|null $currentTag */
    /** @var \Domain\Post\Models\Topic|null $currentTopic */
@endphp

@component('layouts.app', [
    'title' => $title,
])
    <div class="flex items-baseline mt-6 justify-between">
        <div class="flex">
            @isset($currentSource)
                <heading class="mr-3" nowrap="nowrap">
                    {{ $currentSource->website }}
                </heading>
            @endisset

            @isset($currentTopic)
                <heading class="mr-3" nowrap="nowrap">
                    {{ $currentTopic->name }}
                </heading>
            @endisset

            @isset($currentTag)
                <p class="text-sm">
                    <tag :tag="$currentTag"></tag>
                </p>

                @if ($user)
                    @if(! $user->hasMuted($currentTag))
                        <post-button
                            :action="$currentTag->getMuteUrl()"
                            class="ml-2"
                        >
                        <span class="
                            underline
                            text-grey text-xs
                            hover:no-underline
                        ">
                            {{ __('Mute tag') }}
                        </span>
                        </post-button>
                    @endif
                @endif
            @endisset

            @if(!isset($currentTag) && !isset($currentTopic))
                <heading nowrap="nowrap">
                    {{ $title }}
                </heading>
            @endif
        </div>

        @if($user)
            <div class="text-sm">
                <a href="{{ filter('unread') }}" class="button button-small {{ filter_active('unread') ? 'button-active' : '' }}">
                    {{ __('Unread') }}
                </a>
            </div>
        @endif
    </div>

    <post-list
        :posts="$posts"
        :user="$user"
        :donationIndex="$donationIndex"
    ></post-list>
@endcomponent
