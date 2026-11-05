@extends('layouts.public')

@section('title', 'Photo Gallery & Action Highlights - KIU Sports Gala')

@section('content')
<div class="space-y-8"
     x-data="{
        lightboxOpen: false,
        currentIndex: 0,
        images: [],
        openLightbox(index) {
            this.currentIndex = index;
            this.lightboxOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeLightbox() {
            this.lightboxOpen = false;
            document.body.classList.remove('overflow-hidden');
        },
        nextImage() {
            if (this.images.length > 0) {
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
            }
        },
        prevImage() {
            if (this.images.length > 0) {
                this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
            }
        }
     }"
     @keydown.escape.window="closeLightbox()"
     @keydown.arrow-right.window="lightboxOpen ? nextImage() : null"
     @keydown.arrow-left.window="lightboxOpen ? prevImage() : null">

    <!-- Gallery Header Banner (Unified Royal Blue & Athletic Gold) -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-950 border-2 border-amber-400 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="text-xs uppercase tracking-widest font-sports font-black text-amber-300 bg-amber-400/20 px-3 py-1 rounded-full border border-amber-400/40">
                    Visual Chronicle
                </span>
                <h1 class="text-2xl sm:text-4xl font-black font-sports uppercase tracking-wide text-white mt-3">
                    Gala Photo Gallery
                </h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-1 font-medium">
                    Official tournament moments, match action highlights, and championship celebrations grouped by edition.
                </p>
            </div>

            <!-- Edition Year Filter Filter -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('gallery') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ empty($selectedYear) ? 'bg-amber-400 text-slate-950 shadow-md' : 'bg-blue-950 border border-blue-800 text-blue-100 hover:text-amber-300' }}">
                    All Gala Editions
                </a>
                @foreach($editions as $ed)
                    <a href="{{ route('gallery', ['year' => $ed->year]) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ (string)$selectedYear === (string)$ed->year ? 'bg-amber-400 text-slate-950 shadow-md' : 'bg-blue-950 border border-blue-800 text-blue-100 hover:text-amber-300' }}">
                        {{ $ed->year }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @php
        // Flatten all photos into a single JS array for the global lightbox
        $allLightboxItems = [];
        $globalIndex = 0;
    @endphp

    @if(empty($galleryByEdition))
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center shadow-xs">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-amber-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">No Gallery Photos Found</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto">
                No match or edition photos are currently available for the selected view.
            </p>
        </div>
    @else
        <!-- Grouped by Edition -->
        <div class="space-y-10">
            @foreach($galleryByEdition as $editionId => $group)
                @php
                    $edition = $group['edition'];
                    $photos = $group['photos'];
                @endphp

                <section class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-xs">
                    <!-- Edition Section Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 mb-6 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $edition->year }}
                                </span>
                                <h2 class="text-2xl font-black text-slate-900">
                                    {{ $edition->title }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $photos->count() }} Photos &bull; Status: {{ ucfirst($edition->status) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('archive', ['year' => $edition->year]) }}"
                               class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1">
                                View {{ $edition->year }} Archive &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Photo Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                        @foreach($photos as $photo)
                            @php
                                $photoIndex = $globalIndex;
                                $allLightboxItems[] = [
                                    'url' => $photo['image_url'],
                                    'is_video' => $photo['is_video'] ?? false,
                                    'video_url' => $photo['video_url'] ?? null,
                                    'embed_url' => $photo['embed_url'] ?? null,
                                    'is_direct_video' => $photo['is_direct_video'] ?? false,
                                    'caption' => $photo['caption'],
                                    'tag' => $photo['tag'],
                                    'type' => $photo['type'],
                                    'edition' => $edition->title . ' (' . $edition->year . ')',
                                ];
                                $globalIndex++;
                            @endphp

                            <div class="group bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden shadow-2xs hover:shadow-md hover:border-blue-400 transition-all duration-300 flex flex-col cursor-pointer"
                                 @click="openLightbox({{ $photoIndex }})">
                                <!-- Thumbnail -->
                                <div class="relative aspect-video bg-black/40 overflow-hidden">
                                    <img src="{{ $photo['image_url'] }}"
                                         alt="{{ $photo['caption'] }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                         loading="lazy">

                                    <!-- Tag Badge -->
                                    <div class="absolute top-2.5 left-2.5 z-10 flex items-center gap-1.5">
                                        @if($photo['is_video'] ?? false)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-600 text-white shadow-xs">
                                                🎬 REEL
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-black/70 backdrop-blur-md text-amber-300 border border-amber-400/40">
                                            {{ $photo['tag'] }}
                                        </span>
                                    </div>

                                    @if($photo['is_video'] ?? false)
                                        <div class="absolute bottom-2.5 right-2.5 z-10">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-black/80 text-white">
                                                ▶ {{ $photo['duration'] ?? '0:45' }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Hover Overlay Icon -->
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                        <div class="w-12 h-12 rounded-full {{ ($photo['is_video'] ?? false) ? 'bg-rose-600 text-white animate-pulse' : 'bg-amber-400 text-slate-950' }} flex items-center justify-center font-bold shadow-lg transform scale-75 group-hover:scale-100 transition-transform">
                                            @if($photo['is_video'] ?? false)
                                                <svg class="w-6 h-6 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo Info -->
                                <div class="p-3.5 flex-1 flex flex-col justify-between">
                                    <p class="text-xs font-semibold text-slate-800 line-clamp-2 leading-relaxed group-hover:text-blue-600 transition-colors">
                                        {{ $photo['caption'] }}
                                    </p>
                                    <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-500">
                                        <span>{{ $photo['type'] }}</span>
                                        <span class="font-bold {{ ($photo['is_video'] ?? false) ? 'text-rose-600' : 'text-amber-600' }} flex items-center gap-1">
                                            {{ ($photo['is_video'] ?? false) ? '▶ Watch Reel' : 'Zoom • Lightbox' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    <!-- Alpine.js Lightbox Component Modal -->
    <div x-init="images = {{ json_encode($allLightboxItems) }}"
         x-show="lightboxOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-md transition-opacity duration-300"
         style="display: none;">

        <!-- Close Button (Top Right) -->
        <button type="button"
                @click="closeLightbox()"
                class="absolute top-4 right-4 z-50 w-11 h-11 rounded-full bg-white/10 hover:bg-amber-400 text-white hover:text-slate-950 flex items-center justify-center transition-all duration-200 focus:outline-none"
                title="Close Lightbox (Esc)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Previous Button -->
        <button type="button"
                @click.stop="prevImage()"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-50 w-12 h-12 rounded-full bg-white/10 hover:bg-amber-400 text-white hover:text-slate-950 flex items-center justify-center transition-all duration-200 focus:outline-none"
                title="Previous Photo (Left Arrow)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- Next Button -->
        <button type="button"
                @click.stop="nextImage()"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-50 w-12 h-12 rounded-full bg-white/10 hover:bg-amber-400 text-white hover:text-slate-950 flex items-center justify-center transition-all duration-200 focus:outline-none"
                title="Next Photo (Right Arrow)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <!-- Main Lightbox Content Area -->
        <div class="relative max-w-5xl max-h-[92vh] w-full p-4 flex flex-col items-center justify-center"
             @click.outside="closeLightbox()">

            <!-- Active Media Container -->
            <div class="relative max-h-[75vh] w-full flex items-center justify-center overflow-hidden rounded-xl bg-black/60 border border-slate-800 shadow-2xl">
                <template x-if="images[currentIndex] && images[currentIndex].is_video">
                    <div class="w-full max-w-3xl aspect-video bg-black flex items-center justify-center">
                        <template x-if="images[currentIndex].is_direct_video">
                            <video :src="images[currentIndex].video_url" controls autoplay class="w-full h-full object-contain"></video>
                        </template>
                        <template x-if="!images[currentIndex].is_direct_video">
                            <iframe :src="images[currentIndex].embed_url" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </template>
                    </div>
                </template>

                <template x-if="images[currentIndex] && !images[currentIndex].is_video">
                    <img :src="images[currentIndex].url"
                         :alt="images[currentIndex].caption"
                         class="max-h-[75vh] max-w-full object-contain select-none transition-all duration-200">
                </template>
            </div>

            <!-- Lightbox Caption & Details Bar -->
            <template x-if="images[currentIndex]">
                <div class="w-full max-w-3xl mt-4 px-4 py-3 rounded-xl bg-[#060b18]/90 border border-amber-400/40 text-center text-white backdrop-blur-sm">
                    <div class="flex items-center justify-center gap-2 mb-1 text-xs">
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-400 text-slate-950 font-bold uppercase tracking-wider text-[10px]"
                              x-text="images[currentIndex].tag"></span>
                        <span class="text-blue-300 font-semibold" x-text="images[currentIndex].edition"></span>
                        <span class="text-slate-400">&bull;</span>
                        <span class="text-amber-300/80 font-mono text-[11px]" x-text="'Photo ' + (currentIndex + 1) + ' of ' + images.length"></span>
                    </div>
                    <p class="text-sm font-medium text-slate-200" x-text="images[currentIndex].caption"></p>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
