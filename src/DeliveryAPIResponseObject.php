<?php

namespace Hyvor\HyvorBlogs;

class DeliveryAPIResponseObject
{
    /**
     * @var 'file'|'redirect'
     */
    public string $type;

    public int $at;

    /**
     * @var 'template'|'asset'|'media'
     */
    public string $file_type;

    public string $content;

    public string $mime_type;

    // for redirect
    public string $to;

    // for both
    public bool $cache;

    public int $status;

    public static function create(array $data): DeliveryAPIResponseObject
    {
        $obj = new self();
        $obj->type = $data['type'];
        $obj->at = $data['at'];
        $obj->cache = $data['cache'];
        $obj->status = $data['status'];

        if ($data['type'] === 'file') {
            $obj->file_type = $data['file_type'];
            $obj->content = $data['content'];
            $obj->mime_type = $data['mime_type'];
        } else {
            $obj->to = $data['to'];
        }

        return $obj;
    }

    public static function createFromJson(string $json): ?DeliveryAPIResponseObject
    {
        $json = json_decode($json, true);
        if (! $json) {
            return null;
        }

        return self::create($json);
    }
}
