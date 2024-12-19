<template>
    <div class="text-center">
      <v-container>
        <v-row justify="center">
          <v-col cols="8">
            <v-container class="max-width">
              <v-pagination
                v-model="page"
                :length="13"
                class="my-4 text-red-500"
              ></v-pagination>
              <v-btn @click="nextPage" class="v-pagination__next">Next</v-btn>
              <v-btn @click="prevPage" class="v-pagination__prev">Previous</v-btn>
            </v-container>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </template>
  
  <script>
    export default {
      data() {
        return {
          page: 1,
        }
      },
      methods: {
      }
    }
  </script>
  
    <style>
    .v-pagination__next {
      margin-left: 10px;
      color : red;
    }
    </style>