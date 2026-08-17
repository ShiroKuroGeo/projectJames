import { createPinia, defineStore } from "pinia";
import api from "@/api/axios";
import { ref } from "vue";

export const useVenueStore = defineStore('venuesStore', () => {

    const selectedVenue = ref();

    const createVenue = async (venueData) => {
        try {
            const required = [
                'slugs',
                'name',
                'area',
                'latitude',
                'longitude',
                'gcash_number',
                'maya_number',
            ];
            const response = await api.post('create/venue', venueData);
            return response.data.message;
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }


    const getVenueBySlug = async (id) => {
        try {
            console.log(id)
            const response = await api.post('view/venue/slugs', id)

            if (response.data.status === 200) return response.data.data;

            alert('There is no venues available!');

            return [];
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }

    const getList = async () => {
        const response = await api.get('list/venue')

        if (response.data.status === 200) return response.data.data;

        alert('There is no venues available!');

        return [];
    }

    const setVenueAdmin = async (ids) => {
        try {
            const required = [
                'user_id',
                'venue_id',
            ];

            const response = await api.post('admin/create/venue', ids);
            return response.data.message;
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }

    const getAdminVenue = async () => {
        try {
            const response = await api.get('admin/list/venue')

            return response.data.data;
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }

    const setVenueCloseDate = async (payload) => {
        try {
            const required = [
                'venue_id',
                'closed_date',
                'reason',
            ];

            const response = await api.post('admin/create/venue/closedate', payload)

            return response.data.data;
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }

    const getVenueCloseDate = async () => {
        try {
            const response = await api.get('list/venue/closedate')

            return response.data.data
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }

    const getVenueCloseDateById = async (id) => {
        try {
            const response = await api.post('view/venue/closedate', id)

            return response.data.data
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server.';
        }
    }

    const deleteVenueCloseDate = async (id) => {
        try {
            const response = await api.post('admin/remove/venue/closedate', id)

            return response.data.data
        } catch (err) {
            if (err.response) {
                return err.response.data.message || 'Something went wrong.';
            }
            return 'Cannot connect to the server';
        }
    }

    return {
        createVenue, getList, setVenueAdmin, getAdminVenue, setVenueCloseDate, getVenueCloseDate, deleteVenueCloseDate, getVenueCloseDateById, selectedVenue, getVenueBySlug
    }

});