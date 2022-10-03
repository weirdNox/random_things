[Tapping, SampRate] = audioread("~/temporary/downloads/tap.wav");
Tap = Tapping(190579:202005).';

% NOTE(nox): Slow repetitions
SlowSecs = 5;
RRef = repeat(Tap, SampRate, SlowSecs, 1/SlowSecs);
R1 = repeat(Tap, SampRate, SlowSecs, 1);
R2 = repeat(Tap, SampRate, SlowSecs, 2);
R3 = repeat(Tap, SampRate, SlowSecs, 3);
R4 = repeat(Tap, SampRate, SlowSecs, 4);
R5 = repeat(Tap, SampRate, SlowSecs, 5);
R6 = repeat(Tap, SampRate, SlowSecs, 6);
R7 = repeat(Tap, SampRate, SlowSecs, 7);
R8 = repeat(Tap, SampRate, SlowSecs, 8);

ToOut = R1+R3;          audiowrite("~/temporary/slow_13.wav",    ToOut/max(abs(ToOut)), SampRate);
ToOut = R2+R3;          audiowrite("~/temporary/slow_23.wav",    ToOut/max(abs(ToOut)), SampRate);
ToOut = R1+R2+R3+R4;    audiowrite("~/temporary/slow_1234.wav",  ToOut/max(abs(ToOut)), SampRate);
ToOut = R2+R3+R4+R5+R6; audiowrite("~/temporary/slow_23456.wav", ToOut/max(abs(ToOut)), SampRate);
% sound(R1+R3, SampRate);
% pause(SlowSecs);

% figure(1); clf;
% plot(SampRate/1e3*((0:size(RRef,1)-1)-size(RRef,1)/2)/size(RRef,1), fftshift(abs(fft(RRef))), ...
%      SampRate/1e3*((0:size(R1,1)-1)-size(R1,1)/2)/size(R1,1), fftshift(abs(fft(R1))) / (1*SlowSecs), ...
%      SampRate/1e3*((0:size(R3,1)-1)-size(R3,1)/2)/size(R3,1), fftshift(abs(fft(R3))) / (3*SlowSecs));

% NOTE(nox): Fast repetitions
FastSecs = 2;
RfRef = repeat(Tap, SampRate, FastSecs, 1/FastSecs)/sqrt(100);
Rf1 = repeat(Tap, SampRate, FastSecs, 1*100)/sqrt(100);
Rf2 = repeat(Tap, SampRate, FastSecs, 2*100)/sqrt(100);
Rf3 = repeat(Tap, SampRate, FastSecs, 3*100)/sqrt(100);
Rf4 = repeat(Tap, SampRate, FastSecs, 4*100)/sqrt(100);
Rf5 = repeat(Tap, SampRate, FastSecs, 5*100)/sqrt(100);
Rf6 = repeat(Tap, SampRate, FastSecs, 6*100)/sqrt(100);
Rf7 = repeat(Tap, SampRate, FastSecs, 7*100)/sqrt(100);
Rf8 = repeat(Tap, SampRate, FastSecs, 8*100)/sqrt(100);

ToOut = Rf1+Rf3;             audiowrite("~/temporary/fast_13.wav",    ToOut/max(abs(ToOut)), SampRate);
ToOut = Rf2+Rf3;             audiowrite("~/temporary/fast_23.wav",    ToOut/max(abs(ToOut)), SampRate);
ToOut = Rf1+Rf2+Rf3+Rf4;     audiowrite("~/temporary/fast_1234.wav",  ToOut/max(abs(ToOut)), SampRate);
ToOut = Rf2+Rf3+Rf4+Rf5+Rf6; audiowrite("~/temporary/fast_23456.wav", ToOut/max(abs(ToOut)), SampRate);

% sound([Rf4+Rf5+Rf6+Rf8], SampRate);

figure(2); clf;
plot(SampRate/1e3*((0:size(RfRef,1)-1)-size(RfRef,1)/2)/size(RfRef,1), fftshift(abs(fft(RfRef))), ...
     SampRate/1e3*((0:size(Rf1,1)-1)-size(Rf1,1)/2)/size(Rf1,1), fftshift(abs(fft(Rf1))) / (1*100*FastSecs));

function Y = repeat(A, SampRate, TotalSecs, RepeatRate)
    TotalLen = round(TotalSecs*SampRate);
    SampsBetweenRepeats = round(SampRate/RepeatRate);
    NumberOfRepeats = floor(TotalSecs*RepeatRate);
    RepeatConv = [1;zeros(SampsBetweenRepeats-1,1)];
    Y = conv(A, repmat(RepeatConv, NumberOfRepeats, 1));
    Y = Y(1:TotalLen);
end
