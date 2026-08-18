import { createPinia, defineStore } from "pinia";
import api from "@/api/axios";

export const useBookingStore = defineStore('BookingStore', () => {

    const createBooking = async (data) => {
        try {
            const response = await api.post('create/booking', data);
            
            console.log(response.data);
            return response.data.message;
        } catch (err) {
            console.log(err.message);
            alert(err.message);
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }

    const getMyAccount = async () => {
        const response = await api.get('/admin/list/booking')
    }

    const getBookingByCode = async (booking) => {
        try {
            const required = [
                'booking_code',
                'customer_phone'
            ];
            const response = await api.post('/admin/review/booking', booking)
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }

    }

    const getBookingByVenueId = async (id) => {
        try {
            const response = await api.post('/admin/view/booking', id)
            return response.data.data;
        } catch (error) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }

    const getReservation = async (data) => {
        try {
            const response = await api.post('view/booking', data);

            return response.data.data;
        } catch (error) {
            if(error.response) {
                return error.response.data.message || 'Something went wrong';
            }

            return 'Cannot connect to the server.';
        }
    }

    const changeBookingStatus = async (data) => {
        try {
            const response = await api.post('update/booking/status', data);

            return response.data.status;
        } catch (error) {
            if(error.response) {
                return error.response.data.message || 'Something went wrong';
            }

            return 'Cannot connect to the server.';
        }
    }

    const getCheckBookingReservation = async (booking) => {
        try {
            const response = await api.post('check/booking', booking);
            console.log(response.data)
            return response.data.data;
        } catch (error) {
            if(error.response) {
                return error.response.data.message || 'Something went wrong';
            }

            return 'Cannot connect to the server.';
        }
    }

    return {
        createBooking, getMyAccount, getBookingByCode, getBookingByVenueId, getReservation, changeBookingStatus, getCheckBookingReservation
    }

});
