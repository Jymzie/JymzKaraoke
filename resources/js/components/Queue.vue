<template>
  <div>
    <v-row>
        <v-col cols="12" lg="6" md="6" sm="12">
            <v-card color="black">
                <v-card-title>
                    <h1  style="color: white;">
                        List
                    </h1>
                    <v-spacer/>
                    <!-- ANCHOR Randomizer  -->
                    <v-btn class="pt-1" color="#D3D3D3" x-small style="border-radius: 10px 0 0 10px;">
                        <v-text-field label="Random" maxlength="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')" color="white" v-model="randomset" style="width: 50px;"/>
                    </v-btn>
                    <v-btn class="mr-1" :disabled="randomset < 1" :dark="randomset < 1" x-small @click="mGetRandomNumber" style="width: 10px;border-radius: 0 10px 10px 0;">
                        <v-icon>
                            mdi-menu-right
                        </v-icon>
                    </v-btn>

                    <!-- ANCHOR Upload  -->
                    <v-btn @click="toupload = true" small>
                        <v-icon>
                            mdi-upload
                        </v-icon>
                    </v-btn>
                    
                </v-card-title>
                <v-card-text>
                  
                    <v-text-field background-color="#D3D3D3" color="white" outlined dense v-model="search" placeholder="Search"></v-text-field>
                    
                   
                   <!-- ANCHOR List  -->
                    <v-simple-table  class="text-center"   fixed-header :height="($vuetify.breakpoint.height)*.57">
                        <v-progress-circular
                            color="primary"
                            indeterminate
                            size="150"
                            class="mt-10"
                            v-if="!islistloaded">
                            Loading Playlist
                        </v-progress-circular>
                        <div v-else-if="cFilter.length == 0">
                            <h1 class="promptempty">Playlist is Empty</h1>
                        </div>
                        <template v-else>
                            <thead >
                                <tr >
                                    <th class="text-center"  v-for="(header,i) in listhead" :key="i">{{ header }}</th>
                                    <th class="text-center">Play</th>
                                </tr>
                                
                            </thead>
                        
                        
                            <tbody>
                            
                                <tr v-for="(data,j) in cFilter" :key="j">
                                    <td v-for="(header,i) in listhead" :key="i">
                                        {{ data[header] }}
                                    </td>
                                    <td>
                                        
                                        <v-icon @click="mToQueue(data)">
                                            mdi-play
                                        </v-icon>
                                    
                                    </td>     
                                </tr>
                            </tbody>
                        </template>
                       
                       
                    </v-simple-table>

                    <!-- ANCHOR Pagination of List  -->
                    <div class="mt-1 d-flex justify-center">
                        <v-btn x-small @click="mPagination('prev10')" :disabled="page == 1" :dark="page == 1">
                            <v-icon>
                                mdi-chevron-double-left
                            </v-icon>
                        </v-btn>
                        <v-btn x-small class="mx-1" @click="mPagination('prev')" :disabled="page == 1" :dark="page == 1">
                            <v-icon>
                                mdi-chevron-left
                            </v-icon>
                        </v-btn>
                        <v-btn class="pt-1" :color="isPageSel ? '#D3D3D3':'white'" x-small @click="isPageSel = true">
                            <b v-if="!isPageSel" class="mt-n1">{{ page}}</b>
                            <v-text-field v-else autofocus maxlength="4" v-model="page" color="white" style="width: 50px;" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" @blur="isPageSel = false" @keyup.enter="isPageSel = false"/>
                        </v-btn>
                        
                        <v-btn x-small class="mx-1" @click="mPagination('next')" :disabled="page == pages" :dark="page == pages">
                            <v-icon>
                                mdi-chevron-right
                            </v-icon>
                        </v-btn>
                        <v-btn x-small @click="mPagination('next10')" :disabled="page == pages" :dark="page == pages">
                            <v-icon>
                                mdi-chevron-double-right
                            </v-icon>
                        </v-btn>
                    </div>
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
                    <!-- ANCHOR Remote  -->
                    <v-btn @click="openctrl = true">
                        
                        <v-icon>
                            mdi-remote
                        </v-icon>
                    </v-btn>
                  
                </v-card-title>
                <v-card-text>
                    <div v-if="queue.length != 0" class="text-center" >
                        <v-row>
                            <v-col cols="6" xl="2" lg="3" md="3" sm="3"  class="text-right" style="color: white;">
                                <b >Now Playing: </b>
                            </v-col>
                            <v-col >
                                <marquee style="background-color: white; border-radius: 10px;" direction="right">{{ current }}</marquee>
                            </v-col>
                        </v-row>
                      
                   
                    </div>
                   
                    <!-- ANCHOR Queue  -->
                    <v-simple-table  class="text-center" fixed-header :height="($vuetify.breakpoint.height)*.65">
                        <v-progress-circular
                            color="primary"
                            indeterminate
                            size="150"
                            class="mt-10"
                            v-if="!isqueueloaded">
                            Searching...
                        </v-progress-circular>
                        <div v-else-if="queue.length <= 1">
                            <h1 class="promptempty">Queue is Empty</h1>
                        </div>
                        <template v-else>
                            <thead>
                                <tr >
                                    <th class="text-center"  v-for="(header,i) in queuehead" :key="i">{{ header }}</th>
                                    <th class="text-center">Cancel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <draggable v-model="queue" style="display: contents;"  @end="mChangePrio" >
                                    <tr v-for="(data,j) in queue.slice(1)" :key="j" >
                                        <td v-for="(header,i) in queuehead" :key="i">
                                            {{ data[header] }}
                                        </td>
                                        <td>
                                    
                                            <v-icon @click="mToCancel(data)">
                                                mdi-close
                                            </v-icon>
                                        
                                    
                                        </td>
                                    </tr>
                                </draggable>
                            </tbody>
                        </template>
                     
                    </v-simple-table>
                </v-card-text>
            </v-card>
           
        </v-col>
    </v-row>

    <!-- NOTE Controller  -->
    <v-dialog v-model="openctrl" :max-width="cResizeDialog">
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
                <!-- <v-icon @click="mFullscreen" color="white">
                    mdi-fullscreen
                </v-icon>
                <v-icon @click="mReload" color="white">
                    mdi-refresh
                </v-icon> -->
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
                <v-btn class="ml-1" @click="mNext" :disabled="queue.length <= 1" :dark="queue.length <= 1">
                    <v-icon>
                        mdi-skip-next
                    </v-icon>
                </v-btn>
                <v-btn class="ml-1"  @click="mClear"  :disabled="queue.length == 0" :dark="queue.length == 0">
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
                <v-file-input v-model="file" dense outlined background-color="#D3D3D3" placeholder="attach file here..."></v-file-input>
                <v-btn class="mt-n4" block @click="!uploading ? mUpload():''" :disabled="file == null" :dark="file == null">
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
import draggable from 'vuedraggable'

export default {
    components: {
        draggable,
    },
    created(){
        this.mGetList()
        this.mGetQueue()
        
        this.$socket.emit('reqtotaldur')
        
        this.$socket.on('reloadme', (cmd) => {
            this.mGetQueue(cmd)
            
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
        listhead:[],
        list:[],
        queuehead:[],
        queue:[],
        search:'',
        toupload:false,
        file:null,
        uploading:false,
        uploadProgress:0,
        openctrl:false,
        percentage:0,
        duration:0,
        page:1,
        pages:1,
        isPageSel:false,
        randomset:1,
        current:null,
        islistloaded:false,
        isqueueloaded:false,

      };
    },
    methods:{
        mGetList(){
            this.islistloaded = false
            axios.get(`api/play`)
            .then(res=>{
                if(res.data.length !=0){
                    this.list = res.data
                    this.listhead = Object.keys(res.data[0])
                }
                else{
                    this.list = []
                }
             
            })
            .finally(()=>{
                setTimeout(() => {
                    this.islistloaded = true  
                }, 2000);
         
            })
        },
        mGetQueue(val){
            if(!val)
                this.isqueueloaded = false
            axios.get(`api/play?mode=q`)
            .then(res=>{
                if(res.data.length !=0){
                    this.queue = res.data
                    this.queuehead = Object.keys(res.data[0])
                    this.current = this.queue[0].Title
                }
                else{
                    this.queue = []
                }
            })
            .finally(()=>{
                setTimeout(() => {
                    this.isqueueloaded= true  
                }, 1000);
         
            })
            
        },
        // NOTE mPagination 
        mPagination(todo){
           if(todo == 'next'){
                this.page++
           }
           else if(todo == 'prev'){
                this.page--
           }
           else if(todo == 'next10'){
                this.page = this.page+10
            }
            else if(todo == 'prev10'){
                this.page = this.page-10

            }
            
        },
        mToQueue(data){
            axios.post(`api/play`, data)
            .finally(()=>{
                this.$socket.emit('reload_data','put')
            })
        },
        //NOTE mUpload
        mUpload(){
            const formData = new FormData()
            formData.append("content",this.file)
            
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
            this.file = null
        },
        mClear(){
            axios.delete(`api/play/clear`)
            .finally(()=>{
                this.$socket.emit('reload_data','clear')
            })
        },
        mToCancel(data){
            axios.delete(`api/play/cancel?no=${data.Prio}`)
            .finally(()=>{
                this.$socket.emit('reload_data','cancel')
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
        },
        mReload(){
            this.$socket.emit('reqreload')
        },
        mGetRandomNumber() {
            let res = []
            for(let x = 0;x<this.randomset;x++){
                let num = Math.floor(Math.random() * (this.list.length - 0)) + 0;
                res[x] = this.list[num].Title
            }
            this.mToQueue({data:res,length:res.length})
  
        },
        mChangePrio(val){
            if(val.oldIndex != val.newIndex){
                axios.patch(`api/play/changeprio?old=${val.oldIndex+1}&new=${val.newIndex+1}`)
                .finally(() =>{
                    this.$socket.emit('reload_data','changeprio')
                })
            }
        }
     
    
    },
    computed:{
        // NOTE Search 
        cFilter(){
            let result = ''
            if(this.search){
                result = this.list.filter(rec => rec.Title.toUpperCase().includes(this.search.toUpperCase()))
                this.pages = Math.ceil(result.length/10)
            }
            else{
                result = this.list
                this.pages = Math.ceil(result.length/10)
            }
            return result.slice((this.page-1)*10,(this.page)*10)
               
        },
     
        cResizeDialog(){
             return this.$vuetify.breakpoint.xl ? '15%':
             this.$vuetify.breakpoint.lg ? '20%':
             this.$vuetify.breakpoint.md ? '28%':
             this.$vuetify.breakpoint.sm ? '45%': '100%'
        }
    },
    watch:{
        // NOTE pagectrl 
        page(val){
            if(val > this.pages)
                this.page = this.pages
            if(val < 1)
                this.page = 1
        },

        search(n,o){
            if(n != o){
                this.page = 1   
            }
        }
      
    }
}
</script>

<style scoped>
td {
    border: 1px solid;
    height: 100% !important;
    padding: 10px 0 10px 0 !important;
    font-family: DancingScript;
    font-weight: bold !important;
    font-size: 20px !important;
  }
.promptempty{
    margin-top: 20px;
    color: gray;
    font-size: 50px;
    font-family: Bitcount;
}
</style>