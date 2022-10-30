<script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<style>
    .overlay{
        background: #fff;
        padding: 15px !important;
        border-radius: 5px;
        font-size: 14px;
    }

    .overlay .place_name{
        font-weight: bold;
        margin-bottom: 5px;
    }

    .overlay hr{
        margin: 10px 0;
    }

    .btn-close,
    .btn-save{
        border: 1px solid #ccc;
        padding: 3px 5px;
        border-radius: 5px;
    }

    #pagination {margin:10px auto;text-align: center;}
    #pagination a {display:inline-block;margin-right:10px;}
    #pagination .on {font-weight: bold; cursor: default;color:#777;}
    .btn-favorite.active{
        color: green;
    }
</style>
<x-guest-layout>
    <div id="app">
        <div class="relative left-0 top-o right-0 bottom-0 border-r border-gray-300 overflow-hidden">
            <div id="map" class="w-full h-full"></div>
            <div class="absolute left-0 top-0 bottom-0 bg-white h-full z-10 w-96 ease-in duration-150" :class="{'-translate-x-96': navActive}">
                <div class="absolute right-0 top-2/4 -translate-y-2/4 p3 bg-white rounded-r" style="width: 25px; right: -25px" @click="toggleNav">
                    <div class="flex align-middle justify-center">
                        <button class="py-5 btn-favorite">
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
                    <div class="p-5 w-full">
                        <div class="flex w-full relative border-2 border-green-500 rounded mb-5">
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
                        <div class="overflow-auto h-full  pb-10">
                            <div class="grid grid-cols-1 divide-y gap-10">
                                <div v-for="place in places" :key="place.id" v-if="places">
                                    <div>
                                        <img src="https://via.placeholder.com/400x250" alt="" class="mb-3">
                                        <div>
                                            <div class="mb-1 flex justify-between">
                                                <div @click.prevent="openWindowInfo(place)" class="cursor-pointer">
                                                    <strong class="text-lg mr-2">@{{place.place_name}}</strong>
                                                    <small class="text-gray-500">@{{ place.category_group_name }}</small>
                                                </div>
                                                <button v-show="user.id" @click="toggleFavorite(place)" class="btn-favorite" :class="{active: isFavorite(place.id)}">
                                                    <i class="bi bi-bookmark"></i>
                                                </button>
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
                                    </div>
                                </div>
                            </div>
                            <div id="pagination"></div>
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
          axios.get('/api/user').then( (res) => {
            if(res.data){
              this.user = res.data
            }
          }).catch((res) => {

          });
        },
        data() {
          return {
            keyword: '춘천',
            navActive: false,
            places: [],
            markers: [],
            user: [],
            overlay: [],
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

            this.closeAllOverlay();

            // 장소검색 객체를 통해 키워드로 장소검색을 요청합니다
            this.ps.keywordSearch( keyword, this.placesSearchCB);
          },
          placesSearchCB(data, status, pagination) {
            if (status === kakao.maps.services.Status.OK) {
              this.places = data;
              // 정상적으로 검색이 완료됐으면
              // 검색 목록과 마커를 표출합니다
              this.displayPlaces(data);

              // 페이지 번호를 표출합니다
              this.displayPagination(pagination);

            } else if (status === kakao.maps.services.Status.ZERO_RESULT) {

              alert('검색 결과가 존재하지 않습니다.');
              return;

            } else if (status === kakao.maps.services.Status.ERROR) {

              alert('검색 결과 중 오류가 발생했습니다.');
              return;

            }
          },
          displayPlaces(places) {
            var bounds = new kakao.maps.LatLngBounds();
            // 지도에 표시되고 있는 마커를 제거합니다
            this.removeMarker();

            for ( var i=0; i<places.length; i++ ) {
              // 마커를 생성하고 지도에 표시합니다
              var placePosition = new kakao.maps.LatLng(places[i].y, places[i].x);
              var marker = this.addMarker(placePosition, i, places[i]);
              bounds.extend(placePosition);

              (function(marker, place, _this) {
                kakao.maps.event.addListener(marker, 'click', () => {
                  const overlay = _this.displayInfowindow(marker, place);
                });
              })(marker, places[i], this);
            }

            // 검색된 장소 위치를 기준으로 지도 범위를 재설정합니다
            this.map.setBounds(bounds);
          },
          makeMaker(position, idx){
            var markerOpt = {
                position: position, // 마커의 위치
            }

            if(idx >= 0){
              var imageSrc = 'https://t1.daumcdn.net/localimg/localimages/07/mapapidoc/marker_number_blue.png';
              var imageSize = new kakao.maps.Size(36, 37);
              var imgOptions =  {
                  spriteSize : new kakao.maps.Size(36, 691), // 스프라이트 이미지의 크기
                  spriteOrigin : new kakao.maps.Point(0, (idx*46)+10), // 스프라이트 이미지 중 사용할 영역의 좌상단 좌표
                  offset: new kakao.maps.Point(13, 37) // 마커 좌표에 일치시킬 이미지 내에서의 좌표
              };
              var markerImage = new kakao.maps.MarkerImage(imageSrc, imageSize, imgOptions);
              markerOpt = Object.assign({}, markerOpt, {image: markerImage});
            }

            var marker = new kakao.maps.Marker(markerOpt);
            return marker;
          },
          addMarker(position, idx, title) {
            const marker = this.makeMaker(position, idx);
            marker.setMap(this.map); // 지도 위에 마커를 표출합니다
            this.markers.push(marker);  // 배열에 생성된 마커를 추가합니다

            return marker;
          },
          displayPagination(pagination) {
            var paginationEl = document.getElementById('pagination'),
              fragment = document.createDocumentFragment(),
              i;

            // 기존에 추가된 페이지번호를 삭제합니다
            while (paginationEl.hasChildNodes()) {
              paginationEl.removeChild (paginationEl.lastChild);
            }

            for (i=1; i<=pagination.last; i++) {
              var el = document.createElement('a');
              el.href = "#";
              el.innerHTML = i;

              if (i===pagination.current) {
                el.className = 'on';
              } else {
                el.onclick = (function(i) {
                  return function() {
                    pagination.gotoPage(i);
                  }
                })(i);
              }

              fragment.appendChild(el);
            }
            paginationEl.appendChild(fragment);
          },
          removeMarker() {
            for ( var i = 0; i < this.markers.length; i++ ) {
              this.markers[i].setMap(null);
            }
            this.markers = [];
          },
          displayInfowindow(marker, place) {
            // 커스텀 오버레이가 표시될 위치입니다
            var content = '<div style="padding:5px;z-index:1;" class="overlay">';
            content += '<div class="overlay-body">';
            content += '<div class="place_name">'+ place.place_name +'</div>';
            content += '<div>'+ place.address_name +'</div>';
            content += '<div>'+ place.road_address_name +'</div>';
            content += '<div>'+ place.phone +'</div>';
            content += '</div>';
            content += '<hr>';
            content += '<div class="flex justify-between">';
            content += '<div class="btn-close" id="btn-close_'+place.id+'" title="닫기">닫기</div>';
            if(this.user.id){
              content += '<div class="btn-save" id="btn-save_'+place.id+'">저장</div>';
            }
            content += '</div>';
            content += '</div>';

            // 커스텀 오버레이를 생성합니다
            var position = marker.getPosition();
            var overlay = new kakao.maps.CustomOverlay({
              position: position,
              content: content,
              xAnchor: 0.3,
              yAnchor: 0.91,
              map: this.map,
            });

            this.closeAllOverlay();
            this.overlay.push(overlay);

            document.querySelector('#btn-close_'+place.id).addEventListener('click', (e) => {
              if(e.target && e.target.id == 'btn-close_'+place.id){
                this.closeOverlay(overlay);
              }
            });

            document.querySelector('#btn-save_'+place.id).addEventListener('click', () => {
              this.storeLocation(place);
              this.closeOverlay(overlay);
            });

            return overlay;
          },
          closeAllOverlay(){
            for (const key in this.overlay) {
              this.overlay[key].setMap(null);
            }
            this.overlay = [];
          },
          closeOverlay(overlay) {
            overlay.setMap(null);
          },
          toggleFavorite(place){
              if(this.isFavorite(place.id)){
                this.destoryLocation(place);
              } else {
                this.storeLocation(place);
              }
          },
          storeLocation(place) {
            place.lat = place.y;
            place.lng = place.x;
            console.log(place);
            axios.post('/location', place)
              .then( (response) => {
                alert('저장 되었습니다.');
                this.getUserPlaceIds();
              });
          },
          destoryLocation(place) {
            axios.delete('/location/destroy_by_place_id/'+this.user.id+'/'+place.id)
              .then( (response) => {
                alert('삭제 되었습니다.');
                this.getUserPlaceIds();
              });
          },
          openWindowInfo(place){
              const placePosition = new kakao.maps.LatLng(place.y, place.x);
              const marker = this.makeMaker(placePosition);
              const moveLatLon = new kakao.maps.LatLng(place.y, place.x);
              this.displayInfowindow(marker, place);
              this.map.setCenter(moveLatLon);
          },
          isFavorite(placeId){
            for (const key in this.user.placeId) {
                if(this.user.placeId[key] == placeId){
                  return true;
                }
            } return false;
          },
          getUserPlaceIds(){
            axios.get('/location/get_user_place_id/'+this.user.id)
              .then( (response) => {
                console.log(response);
                this.user.placeId = response.data;
              } );
          }
        },
      }).mount('#app')
    </script>
</x-guest-layout>