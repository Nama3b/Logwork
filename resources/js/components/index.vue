<template>
    <div class="body">
        <h1>{{ new Date().toLocaleString('vi-VN', {month: 'long', year: 'numeric'}) }}</h1>
        <div class="calendar">
            <div v-for="day in daysInMonth" :key="day" class="day" @click="openPopup(day)">
                {{ day }}
                <div v-for="event in events[day]" :key="event.time" :class="['event', event.time.toLowerCase()]">
                    {{ event.time }}: {{ event.detail }}
                </div>
            </div>
        </div>

        <div v-if="showPopup" class="popup">
            <h3>Chọn thời gian</h3>
            <button v-for="time in timeSlots" :key="time" @click="selectTime(time)"
                    :class="['time-btn', { selected: selectedTimeSlot === time }]">
                {{ time }}
            </button>
            <input type="text" v-model="eventDetail" placeholder="Nhập nội dung"/>
            <button @click="saveEvent">Lưu</button>
            <button @click="showPopup = false">Hủy</button>
        </div>
    </div>
</template>

<style>
.body {
    font-family: Arial, sans-serif;
    text-align: center;
    margin: 0;
    padding: 0;
}

.calendar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 5px;
    max-width: 100%;
    margin: 20px auto;
    padding: 10px;
}

.day {
    border: 1px solid #ccc;
    padding: 10px;
    min-height: 60px;
    cursor: pointer;
    position: relative;
    background: #f9f9f9;
    border-radius: 5px;
    font-size: 14px;
}

.event {
    color: white;
    padding: 3px;
    margin-top: 3px;
    border-radius: 3px;
    font-size: 12px;
}

.popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 400px;
}

.popup button {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.popup input {
    width: calc(100% - 10px);
    padding: 8px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.time-btn {
    background-color: #f1f1f1;
    border: 1px solid #ccc;
    padding: 10px;
    margin: 5px;
    cursor: pointer;
}

.selected {
    background-color: #00b9b7;
}

#btnSáng.selected, .sáng {
    background-color: #24abab;
}

#btnChiều.selected, .chiều {
    background-color: #ff9800;
}

#btnTối.selected, .tối {
    background-color: #2a3a93;
}

</style>

<script>
import {ref, onMounted} from 'vue';
import axios from 'axios';

export default {
    setup() {
        const daysInMonth = ref([]);
        const selectedDay = ref(null);
        const selectedTimeSlot = ref(null);
        const eventDetail = ref('');
        const events = ref({});
        const showPopup = ref(false);

        const timeSlots = ["Sáng", "Chiều", "Tối"];
        const timeOrder = {"Sáng": 1, "Chiều": 2, "Tối": 3};

        const getDaysInMonth = () => {
            const date = new Date();
            const month = date.getMonth();
            const year = date.getFullYear();
            return new Array(new Date(year, month + 1, 0).getDate()).fill().map((_, i) => i + 1);
        };

        const fetchEvents = async () => {
            try {
                const response = await axios.get('/api/list-event')
                    .then(response => console.log(response.data))
                    .catch(error => console.error('API error:', error));
                events.value = response.data;
            } catch (error) {
                console.error('Error fetching events:', error);
            }
        };

        const openPopup = (day) => {
            const today = new Date().getDate();
            if (day < today) {
                alert("Bạn không thể thêm sự kiện cho ngày đã qua.");
                return;
            }
            selectedDay.value = day;
            selectedTimeSlot.value = null;
            eventDetail.value = '';
            showPopup.value = true;
        };

        const selectTime = (time) => {
            selectedTimeSlot.value = time;
        };

        const saveEvent = async () => {
            if (!selectedTimeSlot.value || !eventDetail.value.trim()) {
                alert("Vui lòng chọn thời gian và nhập nội dung.");
                return;
            }

            try {
                await axios.post('/api/save-event', {
                    date: selectedDay.value,
                    time: selectedTimeSlot.value,
                    detail: eventDetail.value.trim()
                });
                await fetchEvents();
                showPopup.value = false;
            } catch (error) {
                console.error('Error saving event:', error);
            }
        };

        const deleteEvent = async (day, time) => {
            try {
                await axios.post('/api/delete-event', {date: day, time});
                await fetchEvents();
            } catch (error) {
                console.error('Error deleting event:', error);
            }
        };

        onMounted(() => {
            daysInMonth.value = getDaysInMonth();
            fetchEvents();
        });

        return {
            daysInMonth,
            selectedDay,
            selectedTimeSlot,
            eventDetail,
            events,
            showPopup,
            timeSlots,
            openPopup,
            selectTime,
            saveEvent,
            deleteEvent,
            timeOrder
        };
    }
};

</script>
