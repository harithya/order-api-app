import http from "k6/http";
import { check, sleep } from "k6";

import { Counter, Trend } from "k6/metrics";

const successCreated = new Counter("orders_created_201");
const outOfStock = new Counter("orders_rejected_422");
const unexpectedStatus = new Counter("orders_unexpected_status");

export const options = {
    scenarios: {
        race_condition_test: {
            executor: "per-vu-iterations",
            vus: 20, // 20 user virtual melakukan request bersamaan
            iterations: 1, // Masing-masing hanya 1 kali pencet
            maxDuration: "10s",
        },
    },
    thresholds: {
        orders_created_201: ["count<=1"],
        orders_unexpected_status: ["count<=0"],
    },
};

export default function () {
    const url = "http://localhost:8000/api/order";
    const payload = JSON.stringify({
        customer_name: "Name" + __VU,
        customer_email: "name" + __VU + "@example.com",
        customer_phone: "0812345678" + __VU,
        items: [
            {
                product_id: 1,
                quantity: 1,
            },
        ],
    });

    const params = {
        headers: {
            "Content-Type": "application/json",
        },
    };

    const res = http.post(url, payload, params);

    const ok = check(res, {
        "status 201 created": (r) => r.status === 201,
        "status 422 out of stock": (r) => r.status === 422,
    });

    if (res.status === 201) {
        successCreated.add(1);
    } else if (res.status === 422) {
        outOfStock.add(1);
    } else {
        unexpectedStatus.add(1);
    }

    console.log(`VU=${__VU} status=${res.status} body=${res.body}`);
}
