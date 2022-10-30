<script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .overlay{
        background: #fff;
        padding: 15px !important;
        border-radius: 5px;
        font-size: 14px;
        width: 200px;
    }

    .overlay .place_name{
        font-weight: bold;
        margin-bottom: 5px;
    }

    .overlay hr{
        margin: 10px 0;
    }

    .btn-close,
    .btn-memo{
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
    <div class="container mx-auto">
        <div class="my-3">
            <a href="/">홈</a>
        </div>
        <div class="px-4 py-3 border border-slate-400 rounded mt-4">
            <div class="avatar flex">
                <div class="avatar-img shrink-0">
                    <img src="https://i.pravatar.cc/50" alt="" class="rounded-full">
                </div>
                <div class="avatar-info ml-3">
                    <div>{{ $user->name }}</div>
                    <div>{{ $user->email }}</div>
                    <div>
                        <small class="avatar-created_at text-gray-700">{{ $user->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 divide-y divide-gray-300">
            @foreach($user->locations as $location)
                <div class="py-3">
                    <div>{{ $location->place_name }}</div>
                    <small>{{ $location->address_name }}</small>
                </div>
            @endforeach
        </div>
        <div id="app" class="w-full">
            <div id="map" style="height: 500px"></div>
        </div>
        <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey={{ env('KAKAOMAP_APPKEY') }}&libraries=services"></script>
        <script type="module">
          import { createApp } from 'https://unpkg.com/vue@3/dist/vue.esm-browser.js';
          createApp({
            mounted(){
              this.init();
              this.places = @json($user->locations);
              this.displayPlaces(this.places);
              axios.get('/api/user').then( (res) => {
                if(res.data){
                  this.user = res.data;
                }
              }).catch((res) => {

              }).finally( () => {

              });
            },
            data() {
              return {
                show: true,
                pagination: true,
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
                    level: 12 // 지도의 확대 레벨
                  };

                // 지도를 생성합니다
                this.map = new kakao.maps.Map(mapContainer, mapOption);

                // 검색 결과 목록이나 마커를 클릭했을 때 장소명을 표출할 인포윈도우를 생성합니다
                this.infowindow = new kakao.maps.InfoWindow({zIndex:1});
              },
              toggleNav(){
                this.navActive = !this.navActive
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
                content += '<div class="flex justify-between">';
                content += '<div class=place_name >'+place.place_name+'</div>';
                if(this.user.id){
                  const isFavorite = this.isFavorite(place);
                  const active = isFavorite ? 'active' : '';
                  content += '<button class="btn-favorite '+active+'" id="btn-save_'+place.id+'">';
                  content += '<i class="bi bi-bookmark"></i>';
                  content += "</button>";
                }
                content += '</div>';
                content += '<div>'+ place.address_name +'</div>';
                content += '<div>'+ place.road_address_name +'</div>';
                content += '<div>'+ place.phone +'</div>';
                content += '</div>';
                content += '<hr>';
                content += '<div class="flex justify-between">';
                content += '<div class="btn-close" id="btn-close_'+place.id+'" title="닫기">닫기</div>';
                if(this.user.id){
                  content += '<div class="btn-memo" id="btn-memo_'+place.id+'">메모</div>';
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
                  this.toggleFavorite(place);
                  this.closeOverlay(overlay);
                });

                document.querySelector('#btn-memo_'+place.id).addEventListener('click', () => {
                  this.editMemo(place);
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
                if(this.isFavorite(place)){
                  this.destoryLocation(place);
                } else {
                  this.storeLocation(place);
                }
              },
              storeLocation(place) {
                place.lat = place.y;
                place.lng = place.x;
                axios.post('/location', place)
                  .then( (response) => {
                    Swal.fire({
                      icon: 'success',
                      title: '저장완료',
                      text: '저장 되었습니다',
                    });
                    this.getUserPlaceIds();
                  });
              },
              destoryLocation(place) {
                const placeId = this.getPlaceId(place);
                axios.delete('/location/destroy_by_place_id/'+this.user.id+'/'+placeId)
                  .then( (response) => {
                    Swal.fire({
                      icon: 'success',
                      title: '삭제완료',
                      text: '삭제 되었습니다',
                    });
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
              isFavorite(place){
                const placeId = this.getPlaceId(place);
                for (const key in this.user.placeId) {
                  if(this.user.placeId[key] == placeId){
                    return true;
                  }
                } return false;
              },
              getUserPlaceIds(){
                axios.get('/location/get_user_place_id/'+this.user.id)
                  .then( (response) => {
                    this.user.placeId = response.data;
                  } );
              },
              getUserLocations(){
                axios.get('/location/user/'+this.user.id).then( response => {
                  this.places = response.data.list;
                  this.user.placeId = response.data.placeIds;
                  this.displayPlaces(this.places);
                  this.pagination = false;
                });
              },
              getPlaceId(place){
                return place.place_id == undefined ? place.id : place.place_id;
              },
              async fetchPlace(place){
                const placeId = this.getPlaceId(place);
                return await axios.get('/location/get_user_location_by_place_id/'+this.user.id+'/'+placeId);
              },
              async editMemo(place) {
                const placeInfo = await this.fetchPlace(place);

                const {value: text} = await Swal.fire({
                  input: 'textarea',
                  inputLabel: '메모입력',
                  inputValue: placeInfo.data.memo,
                  inputPlaceholder: '메모를 입력하세요',
                  inputAttributes: {
                    'aria-label': '메모를 입력하세요'
                  },
                  showCancelButton: true
                });

                if (text) {
                  place.lat = place.y;
                  place.lng = place.x;
                  place.memo = text;
                  axios.post('/location/edit_memo', place).then( response => {
                    if(response.data){
                      this.getUserPlaceIds();
                      this.closeAllOverlay();
                      Swal.fire({
                        icon: 'success',
                        title: '입력완료',
                        text: '입력완료!',
                      });
                    }
                  });
                }
              }
            },
          }).mount('#app')
        </script>
    </div>
</x-guest-layout>