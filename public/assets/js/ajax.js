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
    } else if (method === "POST") {
      return urlPath;
    } else {
      throw new Error(
        `Ajax.#FinalUrl: Method ${method} is not supported for URL building.`
      );
    }
  }

  static #buildHeader(method, passedHeaders) {
    const tempHeader = { ...Ajax.defaults.headers, ...passedHeaders };
    if (method === "POST") {
      tempHeader["Content-Type"] = "application/json";
    }
    return tempHeader;
  }

  static #buildBody(method, data) {
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
    credentials
  ) {
    const finalHeaders = this.#buildHeader(method, passedHeaders); // Overrides defaults if provided
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
        finalTimeout
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
      // Network/abort error
      throw new AjaxError(err?.message || "Network error", { url: requestUrl });
    }

    if (timerId) clearTimeout(timerId);

    const contentType = response.headers.get("content-type") || "";
    let parsed;
    try {
      if (contentType.includes("application/json")) {
        parsed = await response.json();
      } else {
        throw new Error("Unsupported content type Must be application/json");
      }
    } catch (_) {
      throw new AjaxError("Failed to parse response");
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
  static post(urlPath, data) {
    return this.request("POST", urlPath, data);
  }
}

// Expose globally for easy use in views
window.Ajax = Ajax;
window.AjaxError = AjaxError;
