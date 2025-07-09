<template>
  <div>
    <v-row>
        <v-col cols="12" lg="6" md="6" sm="12">
            <v-card color="black">
                <v-card-title>
                    <h1  style="color: white;">
                        List
                    </h1>
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="10">
                            <v-text-field background-color="white" outlined dense v-model="search" placeholder="Search"></v-text-field>
                        </v-col>
                        <v-col cols="2">
                            <v-btn @click="toupload = true">
                                <v-icon>
                                    mdi-upload
                                </v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                   
                   
                    <v-simple-table  class="text-center"   fixed-header :height="($vuetify.breakpoint.height)*.62">
   
                        <thead >
                            <tr >
                                <th class="text-center"  v-for="(header,i) in header" :key="i">{{ header }}</th>
                                <th class="text-center">Play</th>
                            </tr>
                            
                        </thead>
                      
                    
                        <tbody>
                            <tr v-for="(data,j) in cFilter" :key="j">
                                <td v-for="(header,i) in header" :key="i">
                                    {{ data[header] }}
                                </td>
                                <td>
                                    
                                    <v-icon @click="mToQueue(data)">
                                        mdi-play
                                    </v-icon>
                                
                                </td>     
                            </tr>
                        </tbody>
                    </v-simple-table>
             
                </v-card-text>
            </v-card>
          
        </v-col>
        <v-col cols="12" lg="6" md="6" sm="12">
            <v-card color="black">
                <v-card-title>
                    <h1  style="color: white;">
                        Queue
                    </h1>
                    <v-spacer/>
                    <v-btn @click="openctrl = true">
                        <v-icon>
                            mdi-remote
                        </v-icon>
                    </v-btn>
                  
                </v-card-title>
                <v-card-text>
                    <v-simple-table  class="text-center" fixed-header :height="($vuetify.breakpoint.height)*.7">
                        <thead v-if="queue.length != 0">
                            <tr >
                                <th class="text-center"  v-for="(header,i) in cQueueHeader" :key="i">{{ header }}</th>
                                <th class="text-center">Cancel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data,j) in queue" :key="j">
                                <td v-for="(header,i) in cQueueHeader" :key="i">
                                    {{ data[header] }}
                                </td>
                                <td>
                             
                                    <v-icon v-if="j!=0" @click="mToCancel(data)">
                                        mdi-close
                                    </v-icon>
                                    <b v-else>Playing</b>
                            
                                </td>
                            </tr>
                        </tbody>
                    </v-simple-table>
                </v-card-text>
            </v-card>
           
        </v-col>
    </v-row>

    <!-- NOTE Controller  -->
    <v-dialog v-model="openctrl" persistent  :max-width="cResizeDialog">
        <v-card class="text-center" color="black">
            <v-card-title>
                <div >
                <input
                    type="range"
                    min="0"
                    max="100"
                    step="1"
                    style="width: 150px;"
                    v-model="percentage"
                   @input="mVidDuration"
                />
                </div>
                <v-icon @click="mFullscreen" color="white">
                    mdi-fullscreen
                </v-icon>
                <v-spacer/>
                <v-icon @click="openctrl = false" color="red">
                    mdi-close
                </v-icon>
            </v-card-title>
            <v-card-text>
                <v-btn  @click="mPlay">
                    <v-icon>
                        mdi-play-pause
                    </v-icon>
                </v-btn>
                <v-btn class="ml-1" @click="mNext" v-if="queue.length > 1">
                    <v-icon>
                        mdi-skip-next
                    </v-icon>
                </v-btn>
                <v-btn class="ml-1"  @click="mClear"  v-if="queue.length != 0">
                    <v-icon>
                        mdi-stop
                    </v-icon>
                </v-btn>
               
            </v-card-text>
        </v-card>
    </v-dialog>

    <!-- NOTE Upload Dialog  -->
    <v-dialog v-model="toupload"  persistent :max-width="cResizeDialog">
        <v-card color="black">
            <v-card-title>
                <h2  style="color: white;">
                    Upload
                </h2>
            <v-spacer/>
                    <v-icon @click="mCloseUpload" color="red">mdi-close</v-icon>
            </v-card-title>
            <v-card-text>
                <v-text-field v-model="file.name" dense outlined background-color="white" placeholder="Title"></v-text-field>
                <v-file-input v-model="file.content" dense outlined class="mt-n4" background-color="white" placeholder="attach file here..."></v-file-input>
                <v-btn class="mt-n4" block @click="!uploading ? mUpload():''" v-if="file.name == '' || file.content == null ? false : true">
                    <v-progress-circular
                    color="primary"
                        :rotate="360"
                    :value="uploadProgress"
                    v-if="uploading == true"
                    >{{ uploadProgress }}%</v-progress-circular>

                    <v-icon v-else>
                        mdi-upload
                    </v-icon>
                </v-btn>
            </v-card-text>
        </v-card>
    </v-dialog>
   
  </div>
</template>

<script>
export default {
    created(){
      this.mGetList()
        this.mGetQueue()
        this.$socket.on('reloadme', () => {
            this.mGetQueue()
            
        });
        this.$socket.on('loadtimestamp', (time) => {
            this.percentage = (time/this.duration)*100
        });

        this.$socket.on('taketotaldur', (time) => {
            this.duration = time
            
        });

      
    },
    data() {
      return {
        header:[],
        list:[],
        header2:[],
        queue:[],
        search:'',
        toupload:false,
        file:{name:'',content:null},
        uploading:false,
        uploadProgress:0,
        openctrl:false,
        percentage:0,
        duration:0,

      };
    },
    methods:{
        mGetList(){
            axios.get(`api/play`)
            .then(res=>{
                if(res.data.length !=0){
                    this.list = res.data
                    this.header = Object.keys(res.data[0])
                }
                else{
                    this.list = []
                }
            })
        },
        mGetQueue(){
           
            axios.get(`api/play?mode=q`)
            .then(res=>{
                if(res.data.length !=0){
                    this.queue = res.data
                    this.header2 = Object.keys(res.data[0])
                }
                else{
                    this.queue = []
                }
            })
            
        },
        mToQueue(data){
            axios.post(`api/play`, data)
            .finally(()=>{
                this.$socket.emit('reload_data')
            })
        },
        //NOTE Upload
        mUpload(){
            const formData = new FormData()
            formData.append("name",this.file.name)
            formData.append("content",this.file.content)
            
            this.uploading = true
            axios.post(`${this.$url}/api/play`,formData,
            {
                onUploadProgress: (progressEvent) => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                this.uploadProgress = percentCompleted;
                console.log(this.uploadProgress , 'loaded',progressEvent.loaded,'total',progressEvent.total )
            }}
            )
            .then(res => {
                this.toupload = false
                this.uploading = false
                alert('Video has been added!')
                this.mGetList()
                this.mCloseUpload()
                this.uploadProgress=0
            })
            // .catch(({response})=>{
            //     if(response.status == 400){
            //         console.log(response)
            //         alert(response.data)
            //     }else{
            //         this.$store.dispatch('setStatusCode', response.status)
            //         this.$router.push('/error/' + response.status)
            //     }
            // })
            
        },
        mCloseUpload(){
            this.toupload = false
            this.file = {name:'',content:null}
        },
        mClear(){
            axios.delete(`api/play/clear`)
            .finally(()=>{
                this.$socket.emit('reload_data')
            })
        },
        mToCancel(data){
            axios.delete(`api/play/cancel?no=${data.No}`)
            .finally(()=>{
                this.$socket.emit('reload_data')
            })
        },
        mNext(){
          axios.delete(`api/play/del`)
          .finally(()=>{
            //update queue
            this.$socket.emit('reload_data','next')
            //new set of video
         
          })
          
        },
        mPlay(){
            this.$socket.emit('toggleplay')
        },
        mVidDuration(){
            this.$socket.emit('setdur',this.percentage)
        },
        mFullscreen(){
            this.$socket.emit('reqfullscreen')
        }
    
    },
    computed:{
        cFilter(){
            if(this.search)
                return this.list.filter(rec => rec.Title.toUpperCase().includes(this.search.toUpperCase()))
            else
                return this.list
        },
        cQueueHeader(){
            if(this.header2)
                return this.header2.filter( rec => !rec.includes('Link'))
        },
        cResizeDialog(){
             return this.$vuetify.breakpoint.xl ? '15%':
             this.$vuetify.breakpoint.lg ? '20%':
             this.$vuetify.breakpoint.md ? '28%':
             this.$vuetify.breakpoint.sm ? '45%': '100%'
        }
    }
}
</script>

<style>



</style>