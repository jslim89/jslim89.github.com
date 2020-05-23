---
layout: page
title: ffmpeg
permalink: /short-notes/ffmpeg/
date: 2020-05-22 21:13:51
comments: false
sharing: true
footer: true
---

https://ffmpeg.org/

#### Increase volume of a video file

```
$ ffmpeg -i input.mp4 -strict -2 -vcodec copy -af "volume=20dB" output.mp4
```

##### Reference:

- [How to increase volume in a video without re-encoding the video](http://breakthebit.org/post/53570840966/how-to-increase-volume-in-a-video-without)
- [“The encoder 'aac' is experimental but experimental codecs are not enabled”](http://stackoverflow.com/questions/32931685/the-encoder-aac-is-experimental-but-experimental-codecs-are-not-enabled/35247468#35247468)

---

#### Convert mp4 to gif

```sh
$ ffmpeg -i input.mp4 -vf "fps=10,scale=320:-1" -loop 0 output.gif
```

##### Reference:

- [How do I convert a video to GIF using ffmpeg, with reasonable quality?](https://superuser.com/questions/556029/how-do-i-convert-a-video-to-gif-using-ffmpeg-with-reasonable-quality/556031#556031)

---

#### Reduce video size

Reduce video resolution to 1/3 of it's original

```sh
$ ffmpeg -i XS-race-2019.mp4 -vf "scale=iw/3:ih/3" a_third_the_frame_size.mp4
```

##### Reference:

- [How can I reduce a video's size with ffmpeg?](https://unix.stackexchange.com/questions/28803/how-can-i-reduce-a-videos-size-with-ffmpeg/447521#447521)
