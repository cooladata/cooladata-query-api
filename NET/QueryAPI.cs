public class QueryAPI
    {
        private static string COOLADATA_TOKEN = "";
        private static string COOLADATA_PROJECTID = "";
        public static string COOLADATA_ACCEPT_JSON_HEADER = "application/json";
        private static string COOLADATA_CHARSET = "UTF-8";
        private static string COOLADATA_ENDPOINT = "https://app.cooladata.com/api/v2/projects/" + COOLADATA_PROJECTID + "/cql/";
        private static string COOALDATA_AUTHENTICATION_HEADER = "Token " + COOLADATA_TOKEN;

        public static string runQuery(string query, string acceptType)
        {
            Uri uri = new Uri(COOLADATA_ENDPOINT);
            HttpWebRequest webRequest;
            string result = String.Empty;
            try
            {
                webRequest = (HttpWebRequest)WebRequest.Create(uri);
                webRequest.Method = "POST";
                webRequest.ContentType = "application/x-www-form-urlencoded";
                webRequest.Headers.Add("Authorization", COOALDATA_AUTHENTICATION_HEADER);
                webRequest.Accept = acceptType;

                var encodedQuery= Encoding.UTF8.GetBytes("tq=" + query);
                using (var stream = webRequest.GetRequestStream())
                {
                    stream.Write(encodedQuery, 0, encodedQuery.Length);
                }

                var response = (HttpWebResponse)webRequest.GetResponse();

                if ((int)response.StatusCode != 200)
                {
                    Console.WriteLine("Error! Status: " + response.StatusCode + " " + response.StatusDescription);
                    return null;
                }

                var responseString = new StreamReader(response.GetResponseStream()).ReadToEnd();
                return responseString.ToString();
            }
            catch (Exception ex)
            {
                return null;
            }
        }
    }