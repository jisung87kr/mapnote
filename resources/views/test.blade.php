<script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<x-guest-layout>
    <div id="app">
        <div class="relative left-0 top-o right-0 bottom-0 border-r border-gray-300 overflow-hidden">
            <div id="map" class="w-full h-full"></div>
            <div class="absolute left-0 top-0 bottom-0 bg-white h-full z-10 w-96 ease-in duration-150" :class="{'-translate-x-96': navActive}">
                <div class="absolute right-0 top-2/4 -translate-y-2/4 p3 bg-white rounded-r" style="width: 25px; right: -25px" @click="toggleNav">
                    <div class="flex align-middle justify-center">
                        <button class="py-5">
                            <i class="bi bi-chevron-compact-left"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-stretch h-full">
                    <div class="shrink-0 border-r border-gray-300">
                        <ul class="grid grid-cols-1 divide-y text-center">
                            <li class="p-4">홈</li>
                            <li class="p-4">더보기</li>
                        </ul>
                    </div>
                    <div class="p-5">
                        <div class="d-flex w-full relative border-2 border-green-500 rounded mb-5">
                            <input type="text" class="border-0 w-full focus:ring-0" v-model="keyword" placeholder="검색어 입력"
                           @keypress.enter="searchPlaces"
                            >
                            <div class="absolute right-0 top-2/4 -translate-y-2/4">
                                <button class="border-l border-gray-300 px-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <div class="grid grid-cols-1 divide-y gap-10 overflow-auto h-full">
                                <div v-for="place in places" :key="place.id" v-if="places">
                                    <a href="">
                                        <img src="https://via.placeholder.com/400x250" alt="" class="mb-3">
                                        <div>
                                            <div class="mb-1">
                                                <strong class="text-lg mr-2">@{{place.place_name}}</strong>
                                                <small class="text-gray-500">@{{ place.category_group_name }}</small>
                                            </div>
                                            <div>@{{ place.road_address_name }}</div>
                                            <div class="flex align-middle text-sm">
                                                <div class="mr-2 flex align-middle">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="red" class="bi bi-star-fill mr-1" viewBox="0 0 16 16" style="margin-top: 4px">
                                                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                                    </svg>
                                                    <span class="font-bold">4.44</span>
                                                </div>
                                                <div class="mr-2">
                                                    <span>리뷰 </span>
                                                    <span class="font-bold">3368</span>
                                                </div>
                                                <div>
                                                    <span>평균 </span>
                                                    <span class="font-bold">15,000</span>
                                                    <span>원</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey={{ env('KAKAOMAP_APPKEY') }}&libraries=services"></script>
    <script type="module">
      import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'

      createApp({
        mounted(){
          this.init();
        },
        data() {
          return {
            keyword: 'Hello Vue!',
            navActive: false,
            places: [],
          }
        },
        methods: {
            init(){
              var mapContainer = document.getElementById('map'), // 지도를 표시할 div
                mapOption = {
                  center: new kakao.maps.LatLng(37.566826, 126.9786567), // 지도의 중심좌표
                  level: 3 // 지도의 확대 레벨
                };

              // 지도를 생성합니다
              this.map = new kakao.maps.Map(mapContainer, mapOption);

              // 장소 검색 객체를 생성합니다
              this.ps = new kakao.maps.services.Places();

              // 검색 결과 목록이나 마커를 클릭했을 때 장소명을 표출할 인포윈도우를 생성합니다
              this.infowindow = new kakao.maps.InfoWindow({zIndex:1});
            },
          toggleNav(){
              this.navActive = !this.navActive
          },
          // 키워드 검색을 요청하는 함수입니다
          searchPlaces() {
            var keyword = this.keyword;

            if (!keyword.replace(/^\s+|\s+$/g, '')) {
              alert('키워드를 입력해주세요!');
              return false;
            }

            // 장소검색 객체를 통해 키워드로 장소검색을 요청합니다
            this.ps.keywordSearch( keyword, this.placesSearchCB);
          },
          placesSearchCB(data, status, pagination) {
            if (status === kakao.maps.services.Status.OK) {
              this.places = data;
              console.log(data, status);
              // 정상적으로 검색이 완료됐으면
              // 검색 목록과 마커를 표출합니다
              // displayPlaces(data);

              // 페이지 번호를 표출합니다
              // displayPagination(pagination);

            } else if (status === kakao.maps.services.Status.ZERO_RESULT) {

              alert('검색 결과가 존재하지 않습니다.');
              return;

            } else if (status === kakao.maps.services.Status.ERROR) {

              alert('검색 결과 중 오류가 발생했습니다.');
              return;

            }
          }
        },
      }).mount('#app')
    </script>
</x-guest-layout>