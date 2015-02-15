package com.cooladata;

import java.io.*;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLConnection;

/**
 * Created by Gil Adirim on 2/12/2015.
 */
public class QueryAPI {
    private static final String COOLADATA_TOKEN = "";
    private static final String COOLADATA_PROJECTID = "";
    private static final String COOLADATA_ACCEPT_HTML_HEADER = "text/html";
    private static final String COOLADATA_ACCEPT_JSON_HEADER = "application/json";
    private static final String COOLADATA_CHARSET = "UTF-8";
    private static final String COOLADATA_ENDPOINT = "https://app.cooladata.com/api/v1/projects/" + COOLADATA_PROJECTID + "/cql/";
    private static final String COOALDATA_AUTHENTICATION_HEADER = "Token " + COOLADATA_TOKEN;

    public static String runQuery(String query, String acceptType) throws IOException {
        URLConnection connection = new URL(COOLADATA_ENDPOINT).openConnection();
        connection.setRequestProperty("Authorization",COOALDATA_AUTHENTICATION_HEADER);
        connection.setRequestProperty("Accept", acceptType);
        connection.setDoOutput(true);

        try (OutputStream output = connection.getOutputStream()) {
            output.write(("tq=" + query).getBytes(COOLADATA_CHARSET));
        }

        int status = ((HttpURLConnection)connection).getResponseCode();
        if (status != 200) {
            System.out.println("Error! Status: " + status + " " + ((HttpURLConnection)connection).getResponseMessage());
            return null;
        }
        else {
            StringBuilder sb = new StringBuilder();
            InputStream response = connection.getInputStream();
            try (BufferedReader reader = new BufferedReader(new InputStreamReader(response, COOLADATA_CHARSET))) {
                for (String line; (line = reader.readLine()) != null;) {
                    sb.append(line);
                }
            }
            return sb.toString();
        }
    }

    public static void main(String[] args) throws IOException {
        String query = "Select count(*) from cooladata where date_range(yesterday)";
        System.out.println(runQuery(query, COOLADATA_ACCEPT_HTML_HEADER));
        System.out.println(runQuery(query, COOLADATA_ACCEPT_JSON_HEADER));
    }
}