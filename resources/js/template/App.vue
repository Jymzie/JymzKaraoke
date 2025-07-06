
<template>
    <v-app id="inspire">
         <!-- ANCHOR NAV DRAWER -->
        <v-navigation-drawer
        clipped
         v-model="drawer"
         style="background-color:		#222222;"
         width="150"
         app
         v-show="!$router.history.current.path.includes('/error') && $route.name != 'login'" 
         mobile-breakpoint="0" touchless
        >
           <v-list dense>
              <v-subheader style="color:white">LIST COMPONENTS:</v-subheader>
                 <v-list-item
                    v-for="(item, i) in items"
                    :key="i"
                    :to="item.to"
                    style="color:white"
                 >
                    <v-list-item-icon>
                       <v-icon style="color:white">{{item.icon}}</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                       <v-list-item-title>{{item.text}}</v-list-item-title>
                    </v-list-item-content>
                 </v-list-item>
           </v-list>
        </v-navigation-drawer>
        <!-- ANCHOR APP BAR -->
        <!-- :height="$vuetify.breakpoint.height*0.10" -->
        <v-app-bar style="background-color:black" clipped-left app v-show="!$router.history.current.path.includes('/error') && $route.name != 'login'" >
           <v-app-bar-nav-icon style="color:white" v-if="!$vuetify.breakpoint.xsOnly && !drawer" @click="drawer = !drawer"/>
           <v-icon style="color:white" v-else-if="!$vuetify.breakpoint.xsOnly && drawer" @click="drawer = !drawer" class="mr-8">mdi-close</v-icon>
           <!-- style="font-size: 3.5vh;" -->
           <v-toolbar-title style="color:white">Jymz Karaoke</v-toolbar-title>
           <v-spacer/>
           <div>
              
      
               <!-- NOTE dropdown menu example -->
               <v-menu v-if="$vuetify.breakpoint.xsOnly" offset-y  open-on-hover :close-on-content-click="false">
                  <template v-slot:activator="{ on, attrs }">
                     <v-icon style="color: grey;" v-bind="attrs" v-on="on">mdi-arrow-down-drop-circle
                     </v-icon>
                  </template>
                  <div style="max-height: 45vw; min-height:20vw;">
                     <v-list>
                        <v-list-item
                           v-for="(item, i) in items"
                           :key="i"
                           :to="item.to"
                        >
                           <v-list-item-icon>
                              <v-icon>{{item.icon}}</v-icon>
                           </v-list-item-icon>
                           <v-list-item-content>
                              <v-list-item-title>{{item.text}}</v-list-item-title>
                           </v-list-item-content>
                        </v-list-item>
                    
                     </v-list>
                  </div>
               </v-menu>
           </div>
        </v-app-bar>
  
        <v-main style="background-image:url('images/leaf.jpg');background-size:cover">
           <router-view class="ma-2"></router-view>
        </v-main>
    </v-app>
  </template>
  
  <script>
     export default {
     
      updated(){
         if(this.$router.history.current.path.includes('/error') || this.$router.history.current.path.includes('/login') || this.$vuetify.breakpoint.xsOnly)
            this.drawer = false
      },
      data: () => ({
         drawer: false,
         items:[
          
            {text:'Play', icon:'mdi-play', to:'Play'},
            {text:'Queue', icon:'mdi-loading', to:'Queue'},
           
            
         ]
      }),
      methods:{
         mSetCookies(){
            axios.get(`api/master/setcookies?code=${this.$store.state.credentials.code}&name=${this.$store.state.credentials.name}`)     
         },
         mRouteto(item){
            if(item == '/login'){
               this.$store.commit("logout")
               this.mSetCookies()
            }
            this.$router.push(item)
         },
      }
        
     }
     
  </script>

  <style scoped>
  .white{
   color:white;
  }
  </style>