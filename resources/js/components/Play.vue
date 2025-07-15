<template>
    <div class="app d-flex  justify-center align-center" @click="mRetainfocus" style="height: 85vh;">
      <videoplayer
        class="videoplayer"
        id="videoplayer"
        :src="'storage/'+currentvid"
        :muted="false"
        :autoplay="true"
        :controls="true"
        :loop="false"
        @play="onPlayerPlay"
        @pause="onPlayerPause"
        @ended="onPlayerEnded"
        @loadeddata="onPlayerLoadeddata"
        @waiting="onPlayerWaiting"
        @playing="onPlayerPlaying"
        @canplay="onPlayerCanplay"
        @canplaythrough="onPlayerCanplaythrough"
        @statechanged="playerStateChanged"
        @timeupdate="onPlayerTimeupdate"
      >
      
        <template
          v-slot:controls="{
          //   togglePlay,
          // playing,
          // percentagePlayed,
          // seekToPercentage,
          // duration,
          // convertTimeToDuration,
          // videoMuted,
          // toggleMute,
            toggleFullscreen
            
          }"
        >
        <div class="videoplayer-controls">
          <!-- <div class="videoplayer-controls mt-n2 pt-1" style="background-color: black; color: white;height: 60px; border-radius: 0 0 10px 10px;">
            <v-col cols="1" class="ml-n1">
              <v-btn @click="onNext()" class="videoplayer-controls-toggleplay"
              color="white">
                <v-icon color="black">
                  mdi-skip-next
                </v-icon>
              </v-btn> -->
              <div ref="invisibleElement"
                style="opacity: 0;" tabindex="0" @keypress.f="toggleFullscreen()" @blur="mRetainfocus">
                            For F FullScreen
              </div>
              <!-- <div class="videoplayer-controls-time">
            {{ convertTimeToDuration(time) }} /
            {{ convertTimeToDuration(duration) }}
          </div> -->
              <!-- <videoplayer-track
            :percentage="percentagePlayed"
            @seek="seekToPercentage"
            class="videoplayer-controls-track"
          /> -->
            <!-- </v-col>
            <v-row>
              <v-col cols="12" class="mb-n11">
                <marquee><h3 v-if="LimitQueue.length == 0">SELECT</h3>
                <h3 v-else>Up Next: {{ LimitQueue[0].Title }}</h3></marquee>
              </v-col>
              <v-col cols="11" >
                <v-row>
                  <v-col cols="3"  v-for="(item,i) in LimitQueue" :key="i">
                      <div class="text-center b">
                        {{ item.Title }} 
                      </div>
                    </v-col>
                    <v-col cols="1" v-if="list.length > 5">
                      <div class="text-center c">
                        ...
                      </div>
                      
                    </v-col>
                </v-row>
                
              </v-col>
            </v-row> -->
          
          </div>
        </template>
      </videoplayer>
     
    </div>
  </template>
  <script>
  import videoplayer from "../components/vidplay";
  import videoplayerTrack from "../components/videotrack";


  export default {
    components: {
      videoplayer,
      videoplayerTrack,
    },
    created(){
      this.mGetQueue()
     
      this.$socket.on('reloadme', () => {
            this.mGetQueue()
        });

        this.$socket.on('sendtotaldur', () => {
            
          this.$socket.emit('givetotaldur',this.duration)
        });

    },
    mounted(){
      this.$refs.invisibleElement.focus()

      //Sample watch on mount
      // this.$watch(() => this.additems.EmployeeID, (newValue) => {
      //       if(newValue.length >= 5){
      //           if(this.isadd)
      //               this.mEmpExist(newValue)
      //       }
      //       else{
      //           this.empcodeerr = 'Minimum of 5 characters'
      //           if(newValue.length == 0)
      //               this.empcodeerr = ''
      //           this.matdiadisabled = true
      //           this.mClearAddInfo()
      //       }         
      //   });
    },
    data() {
      return {
        time: 0,
        list:[],
        currentvid:'Sample.mp4',
        duration: 0,
      };
    },
    methods: {
      onPlayerPlay({ event, player }) {
        console.log(event.type);
        player.setPlaying(true);
      },
      onPlayerPause({ event, player }) {
        console.log(event.type);
        player.setPlaying(false);
      },
      onPlayerEnded({ event, player }) {
        console.log(event.type);
        player.setPlaying(false);
        //delete ended song
        this.onNext()
        console.log(event.type);
        player.toggleNext()
       

      },
      onPlayerLoadeddata({ event, player  }) {
        this.duration = player.GetDuration()
        this.$socket.emit('givetotaldur',this.duration)
        console.log(event.type);
      },
      onPlayerWaiting({ event }) {
        console.log(event.type);
      },
      onPlayerPlaying({ event }) {
        console.log(event.type);
      },
      onPlayerTimeupdate({ event }) {
        this.time = event.target.currentTime;
        this.$socket.emit('settimestamp',this.time)
     
        // console.log({ event: event.type, time: event.target.currentTime });
      },
      onPlayerCanplay({ event }) {
        console.log(event.type);
      },
      onPlayerCanplaythrough({ event }) {
        console.log(event.type);
      },
    
      playerStateChanged({ event }) {
        console.log(event.type);
      },

       mGetQueue(){
            axios.get(`api/play?mode=q`)
            .then(res=>{
                this.list = res.data
                
            })
            .finally(()=>{
              if(this.list.length != 0)
              
                  this.currentvid = 'video/'+this.list[0].Title+'.mp4'
            else
              this.currentvid = 'Sample.mp4'
            })
        },
        onNext(){
          axios.delete(`api/play/del`)
          .finally(()=>{
            //update queue
            this.$socket.emit('reload_data','next')
            //new set of video
         
          })
          
        },
       mRetainfocus(){
        setTimeout(() => {
          this.$refs.invisibleElement.focus()
        }, 1)
       }

    },
    computed:{
      LimitQueue(){
      
          let limit = this.list.slice(1)
          limit = limit.splice(0,4)
          return limit
       
        
      }
    },
    watch:{

    }
  };
  </script>
  
  <style>
  
  
  .videoplayer-controls {
    display: flex;
    font: 0.8em sans-serif;
    width: 100%;
  }
  
  .videoplayer-controls-toggleplay,
  .videoplayer-controls-togglemute {
    flex: 1;
  }
  
  .videoplayer-controls-time {
    flex: 2;
    text-align: center;
    line-height: 2;
  }
  
  .videoplayer-controls-track {
    flex: 5;
    line-height: 2;
  }

  div.b {
  white-space: nowrap; 
  width: 130px; 
  overflow: hidden;
  padding: 2px;
  color: aliceblue;
  background-color: gray;
  text-overflow: ellipsis;
  border: 1px solid #000000;
  border-radius: 5%;
  font-size: 18px;
}
div.c {
  white-space: nowrap; 
  width: 40px;
  padding: 2px 0 2px 0;
  border: 1px solid #000000;
  color: aliceblue;
  background-color: gray;
  border-radius: 5%;
  font-size: 18px;
}
  </style>
  