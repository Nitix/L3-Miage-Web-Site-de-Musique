/**
 * Created by Guillaume on 25/11/2014.
 */
///<reference path="headers/jquery/jquery.d.ts" />
///<reference path="Playlist.ts" />
var Player = (function () {
    function Player(playlist) {
        this.isMusicSet = false;
        this.isPlaying = false;
        this.cursorUnlocked = false;
        this.audio = document.getElementById("audio");
        this.source = document.getElementsByTagName("source")[0];
        this.progress = document.getElementById("musicProgress");
        this.cursor = document.getElementById("cursorMusic");
        this.volumeInput = document.getElementById("volume");
        this.volumeMute = document.getElementById("volumeMute");
        this.playButton = document.getElementById("play");
        this.playlist = playlist;
        $(this.audio).on('ended', function () {
            p.showPlayButton();
            p.next();
        });
        $("#play").on('click', function () {
            p.togglePlay();
        });
        $("#previousTrack").on('click', function () {
            p.previous();
        });
        $("#nextTrack").on('click', function () {
            p.next();
        });
        $("#volume").on('change input', function () {
            p.setVolume();
        });
        $("#cursorMusic").on('change', function () {
            p.setPosition();
        });
        $("#cursorMusic").on('input', function () {
            p.unlock();
        });
        $("#volumeGroup").hover(function () {
            $('#volumeControlContainer').show(500).css('display', 'inline-block');
        }, function () {
            $('#volumeControlContainer').show(500).css('display', 'none');
        });
        $("#volumeMute").on('click', function () {
            p.toggleVolumeMute();
        });
    }
    Player.prototype.setTrack = function (track) {
        if (track !== null) {
            this.source.src = track;
            this.audio.load();
            this.audio.pause();
            this.progress.setAttribute("value", "0");
            this.cursor.setAttribute("value", "0");
            this.audio.play();
            this.showPauseButton();
        }
    };
    Player.prototype.play = function () {
        if (this.isMusicSet) {
            this.audio.play();
            this.showPauseButton();
        }
        else {
            this.isMusicSet = true;
            var track = this.playlist.getNextMusic();
            if (track !== null) {
                this.setTrack(track);
            }
            else {
                console.log("NOTIFICATION aucune musique");
            }
        }
    };
    Player.prototype.pause = function () {
        this.audio.pause();
        this.showPlayButton();
    };
    Player.prototype.next = function () {
        var track = this.playlist.getNextMusic();
        if (track !== null)
            this.setTrack(track);
        else
            console.log("NOTIFICATION pas de prochaine musique");
    };
    Player.prototype.previous = function () {
        var track = this.playlist.getPreviousMusic();
        if (track !== null)
            this.setTrack(track);
        else
            console.log("NOTIFICATION pas de précèdente musique");
    };
    Player.prototype.updateProgressBar = function () {
        if (this.isMusicSet) {
            this.progress.setAttribute("max", "" + this.audio.duration);
            this.progress.setAttribute("value", "" + this.audio.currentTime);
            if (!this.cursorUnlocked) {
                this.cursor.setAttribute("max", "" + this.audio.duration);
                this.cursor.value = "" + this.audio.currentTime;
            }
        }
    };
    Player.prototype.setVolume = function () {
        this.audio.volume = +this.volumeInput.value / 100;
    };
    Player.prototype.setPosition = function () {
        this.cursorUnlocked = false;
        this.audio.currentTime = +this.cursor.value;
    };
    Player.prototype.unlock = function () {
        this.cursorUnlocked = true;
    };
    Player.prototype.toggleVolumeMute = function () {
        if (this.audio.muted) {
            this.audio.muted = false;
            this.volumeMute.src = 'css/icons/volume.png';
        }
        else {
            this.audio.muted = true;
            this.volumeMute.src = 'css/icons/muted.png';
        }
    };
    Player.prototype.togglePlay = function () {
        if (this.isPlaying) {
            this.pause();
        }
        else {
            this.play();
        }
    };
    Player.prototype.showPauseButton = function () {
        this.isPlaying = true;
        this.playButton.src = 'css/icons/pause.png';
    };
    Player.prototype.showPlayButton = function () {
        this.isPlaying = false;
        this.playButton.src = 'css/icons/play.png';
    };
    return Player;
})();
var p = new Player(new Playlist());
setInterval(function () {
    p.updateProgressBar();
}, 100);
//# sourceMappingURL=Player.js.map