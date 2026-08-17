import { createPinia, defineStore } from "pinia";
import api from "@/api/axios";

export const useCourtStore = defineStore('CourtStore', () => {

    const createCourt = async (courtData) => {

        try {
            const required = [
                'venue_id',
                'name',
                'tag',
                'price',
                'price_definition'
            ];
            const response = await api.post('admin/create/court', courtData)

            return response.data.message;

        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }

    }

    const getCourts = async (venue_id) => {
        const response = await api.post('list/court', venue_id);

        return response.data.data;
    }

    const setCourtClosedTime = async (courtData) => {
        try {
            const required = [
                'court_id',
                'closed_date',
                'closed_times'
            ];
            const response = await api.post('admin/create/court/closeTime', courtData)

            return response.data.data;

        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }

    }

    const courtCloseTime = async (data) => {
        try {
            const response = await api.post('list/court/closeTime', data);

            return response.data.data;

        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }

    }


    return {
        createCourt, getCourts, setCourtClosedTime, courtCloseTime
    }
});