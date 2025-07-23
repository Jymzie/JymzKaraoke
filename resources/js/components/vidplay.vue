<template>
  <!-- NOTE https://codesandbox.io/p/sandbox/eloquent-mahavira-9kgrc?file=%2Fsrc%2FApp.vue&from-embed&initialpath=%2F  -->
    <div>
      <video
        :src="src"
        :muted="muted"
        :autoplay="autoplay"
        :controls="controls"
        controlsList="nodownload"
        :loop="loop"
        style="border-radius: 10px;"
        :width="width"
        :height="height"
        :poster="poster"
        :preload="preload"
        :playsinline="true"
        ref="player"
      />
      <slot
        name="controls"
        :play="play"
        :pause="pause"
        :toggle-play="togglePlay"
        :playing="playing"
        :percentage-played="percentagePlayed"
        :seek-to-percentage="seekToPercentage"
        :duration="duration"
        :convert-time-to-duration="convertTimeToDuration"
        :video-muted="videoMuted"
        :toggle-mute="toggleMute"
        :toggle-next="toggleNext"
        :toggle-Fullscreen="toggleFullscreen"
      ></slot>
      
    </div>
  </template>
  
  <script>
  const EVENTS = [
    "play",
    "pause",
    "ended",
    "loadeddata",
    "waiting",
    "playing",
    "timeupdate",
    "canplay",
    "canplaythrough",
    "statechanged",
  ];
  
  export default {
    name: "Videoplayer",
    props: {
      src: { type: String, required: true },
      controls: { type: Boolean, required: false, default: false },
      loop: { type: Boolean, required: false, default: false },
      width: { type: Number, required: false, default: 800 },
      height: { type: Number, required: false, default: 450 },
      autoplay: { type: Boolean, required: false, default: false },
      muted: { type: Boolean, required: false, default: false },
      poster: { type: String, required: false },
      preload: { type: String, required: false, default: "auto" },
    },
    data() {
      return {
        playing: false,
        duration: 0,
        percentagePlayed: 0,
        videoMuted: false
      };
    },
    created(){
      this.$socket.on('reloadme', (cmd) => {
        if(cmd == 'next')
        setTimeout(() => {
          this.toggleNext()
        }, 1000);
      });
      this.$socket.on('playpause', () => {
          this.togglePlay()
      });
      this.$socket.on('loaddur', (per) => {
          this.seekToPercentage(per)
      });
      this.$socket.on('dofullscreen', () => {
         this.toggleFullscreen()
      });
      this.$socket.on('reloadplay', () => {

        this.mRefresh()
      });
    },
    mounted() {
      this.bindEvents();
  
      if (this.$refs.player.muted) {
        this.setMuted(true);
      }
 
    },
    methods: {
      toggleFullscreen() {
        const elem = this.$refs.player;
        
        if (!document.fullscreenElement) {
          elem.requestFullscreen().catch(err => {
            console.warn(`Error attempting to enter fullscreen: ${err.message}`);
          });
        } else {
          document.exitFullscreen();
        }
      },
      bindEvents() {
        EVENTS.forEach((event) => {
          this.bindVideoEvent(event);
        });
      },
  
      bindVideoEvent(which) {
        const player = this.$refs.player;
  
        player.addEventListener(
          which,
          (event) => {
            if (which === "loadeddata") {
              this.duration = player.duration;
            }
  
            if (which === "timeupdate") {
              this.percentagePlayed =
                (player.currentTime / player.duration) * 100;
            }
  
            this.$emit(which, { event, player: this });
          },
          true
        );
      },
  
      play() {
        this.$refs.player.play();
        this.setPlaying(true);
      },
  
      pause() {
        this.$refs.player.pause();
        this.setPlaying(false);
      },
  
      togglePlay() {
        if (this.playing) {
          this.pause();
        } else {
          this.play();
        }
      },
  
      setPlaying(state) {
        this.playing = state;
      },
  
      seekToPercentage(percentage) {
        this.$refs.player.currentTime = (percentage / 100) * this.duration;
      },
  
      convertTimeToDuration(seconds) {
        return [parseInt((seconds / 60) % 60, 10), parseInt(seconds % 60, 10)]
          .join(":")
          .replace(/\b(\d)\b/g, "0$1");
      },
  
      mute() {
        this.$refs.player.muted = true;
        this.setMuted(true);
      },
  
      unmute() {
        this.$refs.player.muted = false;
        this.setMuted(false);
      },
  
      toggleMute() {
        if (this.videoMuted) {
          this.unmute();
        } else {
          this.mute();
        }
      },
  
      setMuted(state) {
        this.videoMuted = state;
      },

      toggleNext(){
        this.$refs.player.load();
      },
      GetDuration(){
        return this.duration
      },
      async mRefresh(){
         this.$refs.player.muted = true;
         await this.$refs.player.play();
         this.$refs.player.muted = false;
      }
    },
  }
  </script>

  <style scoped>
 
  </style>
  