<template>
  <FrontLayout>
    <v-container class="fill-height" fluid>
      <v-row align="center" justify="center" no-gutters>
        <v-col cols="12">
          <h1 class="text-center display-1 font-weight-black primary--text">
            VIDEO TUTORIAL
          </h1>
        </v-col>
        <v-col xs="12" md="6" sm="12">
          <youtube video-id="9NrSndASfv8" :player-vars="{ autoplay: 1 }" />
        </v-col>
      </v-row>
    </v-container>
  </FrontLayout>
</template>
<script>
  import FrontLayout from "@/views/layouts/FrontLayout";
  export default {
    name: "VideoTutorial",
    components: {
      FrontLayout,
    },
  };
</script>
