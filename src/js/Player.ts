/**
 * Created by Guillaume on 25/11/2014.
 */
///<reference path="headers/jquery/jquery.d.ts" />
///<reference path="Playlist.ts" />

class Player {
    private currentMusic:string;
    private volume:number;

    private isMusicSet:boolean = false;

    private playlist:Playlist;

    private audio:HTMLAudioElement = <HTMLAudioElement>document.getElementById("audio");
    private source:HTMLSourceElement = <HTMLSourceElement>document.getElementsByTagName("source")[0];

    public constructor(playlist:Playlist) {
        this.playlist = playlist;
    }

    private setTrack(track:string):void {
        if (track !== null) {
            this.source.src = track;
            this.audio.load();
            this.audio.pause();
            this.audio.play();
        }
    }

    public play():void {
        if (this.isMusicSet) {
            this.audio.play();
        } else {
            this.isMusicSet = true;
            var track = this.playlist.getNextMusic();
            if (track !== null)
                this.setTrack(track);
            else
                console.log("NOTIFICATION aucune musique");
        }
    }

    public pause():void {
        this.audio.pause();
    }

    public next():void {
        var track = this.playlist.getNextMusic();
        if (track !== null)
            this.setTrack(track);
        else
            console.log("NOTIFICATION pas de prochaine musique");
    }

    public previous():void {
        var track = this.playlist.getPreviousMusic();
        if (track !== null)
            this.setTrack(track);
        else
            console.log("NOTIFICATION pas de précèdente musique");
    }
}

var p = new Player(new Playlist());
$("#play").on('click', function () {
    p.play()
});
$("#pause").on('click', function () {
    p.pause()
});
$("#previousTrack").on('click', function () {
    p.previous()
});
$("#nextTrack").on('click', function () {
    p.next()
});