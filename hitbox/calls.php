<?php
/**
 * Created by IntelliJ IDEA.
 * User: Alekos
 * Date: 22/03/16
 * Time: 18:10
 */

$memoLive = null;

function isLive($channel_name)
{
    global $memoLive;

    if ($memoLive != null)
        return $memoLive;

    $url = 'https://www.hitbox.tv/api/media/status/' . $channel_name;
    $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 5
            )
        )
    );
    //$response = file_get_contents($url, false, $ctx);
    $response = '{"media_is_live": false}';
    $data = json_decode($response);
    return ($memoLive = isset($data->media_is_live) && $data->media_is_live == 1);
}