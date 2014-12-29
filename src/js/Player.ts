/**
 * Created by Guillaume on 25/11/2014.
 */
///<reference path="headers/jquery/jquery.d.ts" />
///<reference path="Playlist.ts" />
///<reference path="headers/notify/notify.d.ts" />

class Player {
    private isMusicSet:boolean = false;
    private isPlaying:boolean = false;
    private cursorUnlocked:boolean = false;

    private playlist:Playlist;

    private player:HTMLDivElement = <HTMLDivElement>document.getElementById("player");
    private audio:HTMLAudioElement = <HTMLAudioElement>document.getElementById("audio");
    private source:HTMLSourceElement = <HTMLSourceElement>document.getElementsByTagName("source")[0];
    private progress:HTMLProgressElement = <HTMLProgressElement>document.getElementById("musicProgress");
    private cursor:HTMLInputElement = <HTMLInputElement>document.getElementById("cursorMusic");
    private volumeInput:HTMLInputElement = <HTMLInputElement>document.getElementById("volume");
    private volumeMute:HTMLImageElement = <HTMLImageElement>document.getElementById("volumeMute");
    private playButton:HTMLImageElement = <HTMLImageElement>document.getElementById("play");

    public constructor(playlist:Playlist) {
        this.playlist = playlist;
        $(this.audio).on('ended', function () {
            p.showPlayButton();
            p.next()
        });
        $(this.audio).on("error stalled", function(e){
            p.showPlayButton();
            p.error(e);
        });
        $(this.source).on("error stalled", function(e){
            p.showPlayButton();
            p.error(e);
        });
        $("#play").on('click', function () {
            p.togglePlay();
        });
        $("#previousTrack").on('click', function () {
            p.previous()
        });
        $("#nextTrack").on('click', function () {
            p.next()
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

    public setTrack(track:string):void {
        if (track !== null) {
            this.source.src = track;
            this.isMusicSet = true;
            this.audio.load();
            this.audio.pause();
            this.progress.setAttribute("value", "0");
            this.cursor.setAttribute("value", "0");
            this.audio.play();
            this.showPauseButton();
        }
    }

    public play():void {
        if (this.isMusicSet) {
            this.audio.play();
            this.showPauseButton();
        } else {
            this.isMusicSet = true;
            var track = this.playlist.getNextMusic();
            if (track !== null) {
                this.setTrack(track);
            } else {
                $(this.player).notify("Aucune musique en cours ou suivante", {elementPosition: 'top center', className : "warn"});
            }
        }
    }

    public pause():void {
        this.audio.pause();
        this.showPlayButton();
    }

    public next():void {
        var track = this.playlist.getNextMusic();
        if (track !== null)
            this.setTrack(track);
        else
            $(this.player).notify("Aucune musique suivante", {elementPosition: 'top center', className : "warn"});
    }

    public previous():void {
        var track = this.playlist.getPreviousMusic();
        if (track !== null)
            this.setTrack(track);
        else
            $(this.player).notify("Aucune musique précèdente", {elementPosition: 'top center', className : "warn"});
    }

    public error(e):void {
        $(this.player).notify("Erreur lors du chargement de la musique", {elementPosition: 'top center', className : "error"});
    }

    public updateProgressBar():void {
        if (this.isMusicSet) {
            this.progress.setAttribute("max", "" + this.audio.duration);
            this.progress.setAttribute("value", "" + this.audio.currentTime);
            if (!this.cursorUnlocked) {
                this.cursor.setAttribute("max", "" + this.audio.duration);
                this.cursor.value = "" + this.audio.currentTime;
            }
        }
    }

    public setVolume():void {
        this.audio.volume = +this.volumeInput.value / 100;
    }

    public setPosition():void {
        this.cursorUnlocked = false;
        this.audio.currentTime = +this.cursor.value;
    }

    public unlock():void {
        this.cursorUnlocked = true;
    }

    public toggleVolumeMute():void {
        if (this.audio.muted) {
            this.audio.muted = false;
            this.volumeMute.src = 'css/icons/volume.png';
        } else {
            this.audio.muted = true;
            this.volumeMute.src = 'css/icons/muted.png';
        }
    }

    public togglePlay():void {
        if (this.isPlaying) {
            this.pause();
        } else {
            this.play();
        }
    }

    private showPauseButton():void {
        this.isPlaying = true;
        this.playButton.src = 'css/icons/pause.png';
    }

    private showPlayButton():void {
        this.isPlaying = false;
        this.playButton.src = 'css/icons/play.png';
    }
}

var p = new Player(new Playlist());

setInterval(function () {
    p.updateProgressBar()
}, 100);
