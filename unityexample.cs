
public class APIClient : MonoBehaviour{
  private IEnumerator FetchSongsFromServer(int min, int max)
      {
          string url = songAPI + $"getsongsinrange&min={min}&max={max}";
          using (UnityWebRequest request = UnityWebRequest.Get(url))
          {
              yield return request.SendWebRequest();
  
              yield return new WaitUntil(() => request.isDone);
  
              if (request.result == UnityWebRequest.Result.Success)
              {
                  string response = request.downloadHandler.text;
                  // Handle the response data here, such as parsing JSON
                  Debug.Log(response);
  
                  SongData[] songData = JsonHelper.getJsonArray<SongData>(response.ToString());
                  SpotifyPlayer.Instance.currentSongs = songData;
              }
              else
              {
                  Debug.LogError("Error getting song data: " + request.error + $"\nUsing {url}");
                  Debugger.Instance.LogError("Error getting song data: " + request.error + $"\nUsing {url}");
              }
          }
      }
}
