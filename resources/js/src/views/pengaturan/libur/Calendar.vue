<template>
  <div class="app-calendar overflow-hidden border">
    <b-overlay :show="loading" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <div class="row no-gutters">
        <!-- Sidebar -->
        <div class="col app-calendar-sidebar flex-grow-0 overflow-hidden d-flex flex-column" :class="{'show': isCalendarOverlaySidebarActive}">
          <calendar-sidebar :is-event-handler-sidebar-active.sync="isEventHandlerSidebarActive" :kategori_libur="kategori_libur" @reload="handleReload" />
        </div>
        <modal-libur @reload="handleReload" :kategori_libur="kategori_libur" />
        <modal-kategori @reload="handleReload" :data_sekolah="data_sekolah" />
        <!-- Calendar -->
        <div class="col position-relative">
          <div class="card shadow-none border-0 mb-0 rounded-0">
            <div class="card-body pb-0">
              <full-calendar ref="refCalendar" :options="calendarOptions" class="full-calendar" />
            </div>
          </div>
        </div>

        <!-- Sidebar Overlay -->
        <div class="body-content-overlay" :class="{'show': isCalendarOverlaySidebarActive}" @click="isCalendarOverlaySidebarActive = false" />
        <!--calendar-event-handler v-model="isEventHandlerSidebarActive" :event="event" :clear-event-data="clearEventData" @remove-event="removeEvent" @add-event="addEvent" @update-event="updateEvent" /-->
      </div>
    </b-overlay>
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue'
// Full Calendar Plugins
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import CalendarEventHandler from './calendar-event-handler/CalendarEventHandler.vue'
import CalendarSidebar from './calendar-sidebar/CalendarSidebar.vue'
import ModalLibur from './ModalLibur.vue'
import ModalKategori from './ModalKategori.vue'
import eventBus from '@core/utils/eventBus'
import { BOverlay } from 'bootstrap-vue'
export default {
  components: {
    FullCalendar, // make the <FullCalendar> tag available
    dayGridPlugin,
    timeGridPlugin,
    listPlugin,
    interactionPlugin,
    useToast,
    ToastificationContent,
    CalendarSidebar,
    CalendarEventHandler,
    ModalLibur,
    ModalKategori,
    BOverlay,
  },
  data() {
    return {
      calendarOptions: {
        plugins: [dayGridPlugin, interactionPlugin, timeGridPlugin, listPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
          start: 'sidebarToggle, prev,next, title',
          end: 'dayGridMonth,listMonth',
        },
        dateClick: this.handleDateClick,
        eventClick: this.handleEventClick,
        events: [],
        eventClassNames: this.handleEventClassNames,
        /*datesSet: event => {
          var midDate = new Date((event.start.getTime() + event.end.getTime()) / 2);//.getMonth()
          var firstDay = new Date(midDate.getFullYear(), midDate.getMonth(), 1);
          var lastDay = new Date(midDate.getFullYear(), midDate.getMonth() + 1, 0);
          // As the calendar starts from prev month and end in next month I take the day between the range
          this.doSomethingOnThisMonth()
        },*/
        locale: 'id',
        locales: 'id',
        buttonText: {
          today:    'Hari Ini',
          month:    'Bulan',
          week:     'Pekan',
          day:      'Hari',
          list:     'Agenda'
        },
        customButtons: { 
          prev: { // this overrides the prev button
            text: "Sebelumnya", 
            click: () => {           
              let calendarApi = this.$refs.refCalendar.getApi();
              this.doSomethingOnPrevMonth(calendarApi);
              calendarApi.prev();
            }
          },
          next: { // this overrides the next button
            text: "Berikutnya",
            click: () => {
              let calendarApi = this.$refs.refCalendar.getApi();
              this.doSomethingOnNextMonth(calendarApi);
              calendarApi.next();
            }
          }
        },
      },
      isCalendarOverlaySidebarActive: false,
      isEventHandlerSidebarActive: false,
      calendarsColor: {
        Business: 'primary',
        Holiday: 'success',
        Personal: 'danger',
        Family: 'warning',
        ETC: 'info',
      },
      kategori_libur: [],
      data_sekolah: [],
      firstDay: null,
      lastDay: null,
      loading: false,
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    doSomethingOnNextMonth(data){
      this.lastDay = new Date(data.currentData.currentDate.getFullYear(), data.currentData.currentDate.getMonth() + 2, 0);
      this.firstDay = new Date(this.lastDay.getFullYear(), this.lastDay.getMonth(), 1);
      this.loadPostsData()
    },
    doSomethingOnPrevMonth(data){
      this.lastDay = new Date(data.currentData.currentDate.getFullYear(), data.currentData.currentDate.getMonth(), 0);
      this.firstDay = new Date(this.lastDay.getFullYear(), this.lastDay.getMonth(), 1);
      this.loadPostsData()
    },
    loadPostsData(){
      this.loading = true
      this.$http.get('/libur', {
        params: {
          start: this.lastDay ?? this.startDate(),
          end: this.firstDay ?? this.endDate(),
        }
      }).then(response => {
        this.loading = false
        let getData = response.data
        this.kategori_libur = getData.kategori_libur
        this.calendarOptions.events = getData.hari_libur
        this.data_sekolah = getData.data_sekolah
      });
    },
    startDate() {
      var date = new Date();
      var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
      return firstDay
    },
    endDate() {
      var date = new Date();
      var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
      return lastDay
    },
    handleDateClick(arg){
      eventBus.$emit('open-modal-libur', {
        aksi: 'add',
        data: arg
      });
    },
    handleEventClick(arg){
      let getData = arg.event._def
      eventBus.$emit('open-modal-libur', {
        aksi: 'edit',
        data: getData
      });
    },
    handleEventClassNames({ event: calendarEvent }){
      console.log(calendarEvent);
      //const colorName = this.calendarsColor[calendarEvent._def.extendedProps.calendar]
      //console.log(calendarEvent);
      return [
        `bg-light-danger`,
      ]
    },
    handleReload(){
      this.loadPostsData()
    }
  },
}
</script>

<style lang="scss">
@import "@resources/scss/vue/apps/calendar.scss";
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>