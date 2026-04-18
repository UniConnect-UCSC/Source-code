"use strict";

class AjaxError extends Error {
  constructor(message, { status, statusText, url, data, response } = {}) {
    super(message);
    this.name = "AjaxError";
    this.status = status;
    this.statusText = statusText;
    this.url = url;
    this.data = data; // parsed body (if any)
    this.response = response; // original Response object
  }
}

class Ajax {
  static defaults = {
    urlPath: "",
    headers: { "X-Requested-With": "XMLHttpRequest" },
    credentials: "same-origin", // include cookies
    timeout: 0,
    allowedMethods: ["GET", "POST"],
  };

  static #FinalUrl(urlPath, method, data) {
    if (method === "GET") {
      if (data && typeof data === "object") {
        const queryString = new URLSearchParams(data).toString();
        const separator = urlPath.includes("?") ? "&" : "?";
        return urlPath + separator + queryString;
      }
      return urlPath;
    } else if (method === "POST") {
      return urlPath;
    } else {
      throw new Error(
        `Ajax.#FinalUrl: Method ${method} is not supported for URL building.`,
      );
    }
  }

  static #buildHeader(passedHeaders) {
    // passed headers override defaults
    return { ...Ajax.defaults.headers, ...passedHeaders };
  }

  static #buildBody(method, data) {
    if (data instanceof FormData) {
      return data;
    }

    if (method === "POST") {
      return JSON.stringify(data);
    }
    return null;
  }
  // Core request
  static async request(
    method = "POST",
    urlPath,
    data = null,
    passedHeaders = {},
    timeout = 0,
    credentials,
    abortSignal = null,
  ) {
    const finalHeaders = this.#buildHeader(passedHeaders); // Overrides defaults if provided
    const finalTimeout =
      typeof timeout === "number" && timeout > 0
        ? timeout
        : Ajax.defaults.timeout;
    const finalCredentials = credentials || Ajax.defaults.credentials;

    if (!Ajax.defaults.allowedMethods.includes(String(method).toUpperCase())) {
      throw new Error(`Ajax.request: Method ${method} is not allowed.`);
    }
    const finalMethod = String(method).toUpperCase();

    const requestUrl = this.#FinalUrl(urlPath, finalMethod, data);
    const body = this.#buildBody(finalMethod, data);

    const controller = new AbortController();
    let timerId = null;
    if (finalTimeout > 0) {
      timerId = setTimeout(
        () => controller.abort(`Timeout after ${finalTimeout}ms`),
        finalTimeout,
      );
    }

    // Listen to external abort signal if provided
    if (abortSignal) {
      abortSignal.addEventListener("abort", () =>
        controller.abort(abortSignal.reason),
      );
    }

    let response;
    try {
      response = await fetch(requestUrl, {
        method: finalMethod,
        headers: finalHeaders,
        body: body,
        credentials: finalCredentials,
        signal: controller.signal,
      });
    } catch (err) {
      if (timerId) clearTimeout(timerId);

      // Check if request was aborted
      if (controller.signal.aborted) {
        throw new AjaxError(`user_aborted Reason: ${controller.signal.reason}`);
      }

      // Network error
      throw new AjaxError(err?.message || "Network error");
    }

    if (timerId) clearTimeout(timerId);

    const contentType = response.headers.get("content-type") || "";
    let parsed;

    if (!contentType.includes("application/json")) {
      throw new AjaxError(
        "Unsupported content type. Expected application/json",
      );
    }

    try {
      parsed = await response.json();
    } catch (err) {
      throw new AjaxError("Failed to parse JSON response");
    }

    if (!response.ok) {
      throw new AjaxError(`HTTP ${response.status} ${response.statusText}`, {
        status: response.status,
        statusText: response.statusText,
        url: requestUrl,
        data: parsed,
        response,
      });
    }

    return parsed;
  }

  // Convenience methods
  static jsonPost(urlPath, data, abortSignal = null) {
    return this.request(
      "POST",
      urlPath,
      data,
      { "Content-Type": "application/json" },
      undefined,
      undefined,
      abortSignal,
    );
  }

  static formDataPost(urlPath, formData, abortSignal = null) {
    return this.request(
      "POST",
      urlPath,
      formData,
      undefined,
      undefined,
      undefined,
      abortSignal,
    );
  }

  static fireAndForget(urlPath, data) {
    var length = 0;
    var selectedHeader = 0;
    const allHeaders = [
      "text/plain",
      "application/json",
      "multipart/form-data",
    ];

    switch (typeof data) {
      case "number":
      case "boolean":
        data = data.toString();

      case "string":
        length = data.length;
        selectedHeader = 0;
        break;

      case "object":
        data = JSON.stringify(data);
        length = data.length;
        selectedHeader = 1;
        break;

      default:
        console.warn(
          "Ajax.fireAndForget: Data is not a string, number, boolean, or object. No action taken.",
        );
        return;
    }

    if (length > 64000) {
      console.warn(
        "Ajax.fireAndForget: Data size exceeds 64KB after conversion, request may be dropped.",
      );
    }

    const blobData = new Blob([data], { type: allHeaders[selectedHeader] });
    navigator.sendBeacon(urlPath, blobData);
  }
}

// Expose globally for easy use in views
window.Ajax = Ajax;
window.AjaxError = AjaxError;
