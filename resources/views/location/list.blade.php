<script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-guest-layout>
    <div class="container mx-auto">
        <div class="my-3">
            <a href="/">홈</a>
        </div>
        <div class="grid gap-3">
        @foreach($locations as $location)
            <div class="px-4 py-3 border border-slate-400 rounded transition ease-in-out hover:-translate-y-0.5 hover:duration-500">
                <a href="{{ route('location.getUser', $location->user->id) }}">
                    <div class="avatar flex">
                        <div class="avatar-img shrink-0">
                            <img src="https://i.pravatar.cc/50?u={{ $location->user->email }}" alt="" class="rounded-full">
                        </div>
                        <div class="avatar-info ml-3">
                            <div>
                                <span>{{ $location->place_name }}</span>
                                <small class="text-gray-500">외 {{ count($location->user->locations) }}개 의 장소</small>
                            </div>
                            <div>
                                <small class="avatar-name font-bold mr-1">{{ $location->user->name }}</small>
                                <small class="avatar-created_at text-gray-700">{{ $location->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
        </div>
    </div>
</x-guest-layout>