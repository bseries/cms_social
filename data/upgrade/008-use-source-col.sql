update media set source = 'stream' where id in (select cover_media_id from social_stream);
