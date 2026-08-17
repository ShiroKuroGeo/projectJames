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

    return { submitPayment }

});