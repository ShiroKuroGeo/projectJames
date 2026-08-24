import { defineStore } from "pinia";
import api from "@/api/axios";

export const usePaymentStore = defineStore('paymentStore', () => {

    const submitPayment = async (amount, booking_code) => {
        try {
            const response = await api.post('checkout', { amount: amount, booking_code: booking_code });
            return response.data;
        } catch (err) {
            console.error('Checkout failed:', err);
            throw err;
        }
    }

    const listPaymentMethod = async (id) => {
        try {
            const response = await api.post('list/payment/method', { bookingCode: id })

            return response.data;
        } catch (error) {
            console.error('Get payment method failed: ', error)
            throw error;
        }
    }

    const submitPaymentQR = async (data) => {
        try {
            const response = await api.post('pay', data)

            return response.data;
        } catch (error) {
            console.error(error);
            throw error
        }
    }

    return { submitPayment, listPaymentMethod, submitPaymentQR }

});