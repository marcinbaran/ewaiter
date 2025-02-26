export const getHostname = () => {
    const port =
        window.location.port &&
        window.location.port !== 80 &&
        window.location.port !== 443 &&
        `:${window.location.port}`;
    return `${window.location.protocol}//${window.location.hostname}${port}`;
};

export const getPath = () => {
    return window.location.pathname;
};

export const getTokenCSRF = () => {
    const token = document.querySelector("meta[name=\"csrf-token\"]")?.content;
    return token ?? null;
};

export const sendRequest = async (
    url,
    method = "GET",
    body = null,
    params = null
) => {
    if (params) {
        url += `/${params}`;
    }
    const response = await fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": getTokenCSRF()
        },
        body: body ? JSON.stringify(body) : null
    });
    if (response.status === 204) {
        return response.status;
    } else {
        const responseData = await response.json();
        return responseData;
    }
};

export const debounce = (func, wait, immediate) => {
    let timeout;
    return function() {
        const context = this, args = arguments;
        const later = () => {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};

export const locale = () => {
    const lang = document.querySelector("html")?.getAttribute("lang");
    return lang ?? "pl";
};

export const compareArrays = (array1, array2) => {
    let result = true;
    array1.forEach((item, index) => {
        if (item !== array2[index]) {
            result = false;
        }
    });
    return result;
};
