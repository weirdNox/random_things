[Tapping, SampRate] = audioread("~/temporary/downloads/tap.wav");
Tap = Tapping(190579:202005).';


NoteSecs = 0.25;
NoteSamps = NoteSecs*SampRate;
BaseFreq = 100;

Synth = synth(SampRate);

IntroConv = zeros(5*SampRate, 1);
IntroTime = (0:length(IntroConv)-1) * Synth.Delta;
for Idx = 1:length(IntroConv)
    Freq = interp1([0 3 5], [2 2 BaseFreq*2*6/5], IntroTime(Idx), 'linear', 0);
    IntroConv(Idx) = Synth.step(1/Freq);
end

Seq = [16 16 11 12 14 14 12 11 09 09 09 12 16 16 14 12 11 11 11 12 14 14 16 16 12 12 11 11 09 09 09 09,...
       14 14 14 17 21 21 19 17 16 16 16 12 16 16 14 12 11 11 11 12 14 14 16 16 12 12 11 11 09 09 09 09];
SeqConv = zeros(NoteSamps*length(Seq), 1);
for Idx = 1:length(Seq)
    Note = Seq(Idx);
    for SampIdx = 1:NoteSamps
        SeqConv(NoteSamps*(Idx-1) + SampIdx) = Synth.stepNote(BaseFreq, Note);
    end
end

Out = conv(Tap, [IntroConv;SeqConv;flipud(IntroConv)]);
