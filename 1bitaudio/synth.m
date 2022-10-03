classdef synth < handle
    properties
        SampRate double {mustBePositive}
        Delta double {mustBePositive}
        Time double = 0
    end
    properties (Constant)
        Notes = [1 25/24 9/8 6/5 5/4 4/3 45/32 3/2 8/5 5/3 9/5 15/8];
    end
    methods
        function Obj = synth(SampRate)
            Obj.SampRate = SampRate;
            Obj.Delta = 1/SampRate;
        end

        function Out = step(Obj, Limit)
            NewTime = Obj.Time + Obj.Delta;
            if NewTime >= Limit
                Out = 1;
                Obj.Time = NewTime - Limit;
            else
                Out = 0;
                Obj.Time = NewTime;
            end
        end

        function Out = stepNote(Obj, BaseFreq, NoteIdx)
            Octave = floor(NoteIdx/12);
            Freq = BaseFreq * (Octave+1) * Obj.Notes(1 + mod(NoteIdx,12));
            Out = Obj.step(1/Freq);
        end
    end
end
