import axios from "axios";

export const client = axios.create({
  baseURL: process.env.NODE_ENV == 'production' ? process.env.PROD_API_BASEURL : process.env.DEV_API_BASEURL,
  headers: {
    "Content-Type": "application/json"
  }
})