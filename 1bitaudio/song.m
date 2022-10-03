[Song, SampRate] = audioread("~/song.wav");
Song = single(Song(:,1));

P = 3;
Q = 1;
AvgCount = 30;

SampRateUp = SampRate * P/Q;
Song = 0.85 * Song ./ max(abs(Song));
SongUp = single(resample(double(Song), P, Q));

switch(1)
    case 1
        Noise = single(2*rand(length(SongUp), AvgCount) - 1);
        NoiseFiltB = fir1(12, 21e3/(SampRateUp/2), 'high');
        NoiseBlue = filter(NoiseFiltB, 1, Noise);
        NoiseBlue = NoiseBlue./max(abs(NoiseBlue));
        Dither = 0.5*NoiseBlue;
    case 2
        Noise = rand(length(SongUp), AvgCount) - rand(length(SongUp), AvgCount);
        Dither = Noise;
end

Song1BitUp = single(2*((SongUp + Dither) >= 0) - 1);
Song1Bit = single(resample(double(Song1BitUp), Q, P));
Song1Bit = mean(Song1Bit, 2);
Song1Bit = Song1Bit ./ max(abs(Song1Bit));
