package com.techcorp.internaltools.exception;

import lombok.AllArgsConstructor;
import lombok.Data;

import java.util.Map;

@Data
@AllArgsConstructor
public class ErrorResponse {
    private String error;
    private String message;
    private Map<String, String> details;

    public ErrorResponse(String error, String message) {
        this.error = error;
        this.message = message;
        this.details = null;
    }
}
