Public Class Point2D
    Public X As Double
    Public Y As Double

    Public Sub New(ByVal xPoint As Double, ByVal yPoint As Double)
        Me.X = xPoint
        Me.Y = yPoint
    End Sub
End Class

Public Structure Tokenizer
    Dim stream As String
    Dim index As Integer
End Structure

Public Structure Token
    Enum TokenType
        Number
        Identifier
        Variable

        Plus
        Minus
        Star
        Slash
        Power

        LeftParenthesis
        RightParenthesis

        UnaryMinus
        Comma

        Unknown
        EndToken
    End Enum

    Dim type As TokenType
    Dim text As String
    Dim number As Double
End Structure

Public Class Form1
    Private Sub inputKeyDown(ByVal sender As Object, ByVal e As KeyEventArgs) Handles MathInput.KeyDown
        If (e.KeyCode = Keys.Return) Then
            handleInput()
        End If
    End Sub

    Public Sub eatWhitespace(ByRef tokenizer As Tokenizer)
        If tokenizer.index < tokenizer.stream.Length Then
            While tokenizer.stream.ElementAt(tokenizer.index) = " "
                tokenizer.index += 1
                If tokenizer.index >= tokenizer.stream.Length Then
                    Exit While
                End If
            End While
        End If
    End Sub

    Public Function precedence(ByRef token As Token) As Integer
        Select Case token.type
            Case Token.TokenType.Plus, Token.TokenType.Minus
                Return 2
            Case Token.TokenType.Star, Token.TokenType.Slash
                Return 3
            Case Token.TokenType.UnaryMinus
                Return 4
            Case Token.TokenType.Power
                Return 5
        End Select
        Return 0
    End Function

    Public Function associativityLeft(ByRef token As Token) As Boolean
        Select Case token.type
            Case Token.TokenType.Plus, Token.TokenType.Minus, Token.TokenType.Star, Token.TokenType.Slash
                Return True
            Case Token.TokenType.UnaryMinus, Token.TokenType.Power
                Return False
        End Select

        Return 0
    End Function

    Public Function getToken(ByRef tokenizer As Tokenizer) As Token
        Dim token = New Token
        eatWhitespace(tokenizer)

        If tokenizer.index < tokenizer.stream.Length Then
            Dim startIndex = tokenizer.index
            tokenizer.index += 1
            Dim c = tokenizer.stream.ElementAt(startIndex)

            Select Case c
                Case "+"
                    token.type = Token.TokenType.Plus
                Case "-"
                    token.type = Token.TokenType.Minus
                Case "*"
                    token.type = Token.TokenType.Star
                Case "/"
                    token.type = Token.TokenType.Slash
                Case "^"
                    token.type = Token.TokenType.Power
                Case "("
                    token.type = Token.TokenType.LeftParenthesis
                Case ")"
                    token.type = Token.TokenType.RightParenthesis
                Case ","
                    token.type = Token.TokenType.Comma

                Case Else
                    If IsNumeric(c) Or c = "." Then
                        token.type = Token.TokenType.Number
                        While tokenizer.index < tokenizer.stream.Length
                            Dim testC = tokenizer.stream.ElementAt(tokenizer.index)
                            If IsNumeric(testC) Or testC = "." Then
                                tokenizer.index += 1
                            Else
                                Exit While
                            End If
                        End While
                        token.text = tokenizer.stream.Substring(startIndex, tokenizer.index - startIndex)
                        Double.TryParse(token.text, Globalization.NumberStyles.Float, Globalization.CultureInfo.InvariantCulture, token.number)

                    ElseIf Char.IsLetter(c) Then
                        Dim availableVariables() As String = {"x", "e", "pi"}
                        Dim availableFunctions() As String = {"abs", "sqrt", "sin", "cos", "tan"}

                        While tokenizer.index < tokenizer.stream.Length
                            Dim testC = tokenizer.stream.ElementAt(tokenizer.index)
                            If Char.IsLetter(testC) Then
                                tokenizer.index += 1
                            Else
                                Exit While
                            End If
                        End While
                        token.text = tokenizer.stream.Substring(startIndex, tokenizer.index - startIndex).ToLower()
                        If Not Array.FindIndex(availableVariables, Function(s) s = token.text) = -1 Then
                            token.type = Token.TokenType.Variable
                        ElseIf Not Array.FindIndex(availableFunctions, Function(s) s = token.text) = -1 Then
                            token.type = Token.TokenType.Identifier
                        Else
                            token.type = Token.TokenType.Unknown
                        End If
                    Else
                        token.type = Token.TokenType.Unknown
                    End If
            End Select

            Return token
        End If

        token.type = Token.TokenType.EndToken
        Return token
    End Function

    Public Function calculateResult(ByVal outputQueue As Queue(Of Token), ByRef point As Point2D) As Double
        Dim rpnStack = New Stack(Of Double)
        Dim index As Integer = 0
        While index < outputQueue.Count
            Dim token As Token = outputQueue.ElementAt(index)
            index += 1
            Select Case token.type
                Case Token.TokenType.Number
                    rpnStack.Push(token.number)
                Case Token.TokenType.Variable
                    Select Case token.text
                        Case "x"
                            rpnStack.Push(point.X)
                        Case "e"
                            rpnStack.Push(Math.E)
                        Case "pi"
                            rpnStack.Push(Math.PI)
                    End Select
                Case Token.TokenType.Identifier
                    Select Case token.text
                        Case "sqrt"
                            Dim a = rpnStack.Pop()
                            rpnStack.Push(Math.Sqrt(a))
                        Case "abs"
                            Dim a = rpnStack.Pop()
                            rpnStack.Push(Math.Abs(a))
                        Case "sin"
                            Dim a = rpnStack.Pop()
                            rpnStack.Push(Math.Sin(a))
                        Case "cos"
                            Dim a = rpnStack.Pop()
                            rpnStack.Push(Math.Cos(a))
                        Case "tan"
                            Dim a = rpnStack.Pop()
                            rpnStack.Push(Math.Tan(a))
                    End Select
                Case Token.TokenType.Plus
                    If rpnStack.Count >= 2 Then
                        Dim b = rpnStack.Pop()
                        Dim a = rpnStack.Pop()
                        rpnStack.Push(a + b)
                    End If
                Case Token.TokenType.Minus
                    If rpnStack.Count >= 2 Then
                        Dim b = rpnStack.Pop()
                        Dim a = rpnStack.Pop()
                        rpnStack.Push(a - b)
                    End If
                Case Token.TokenType.Slash
                    If rpnStack.Count >= 2 Then
                        Dim b = rpnStack.Pop()
                        Dim a = rpnStack.Pop()
                        rpnStack.Push(a / b)
                    End If
                Case Token.TokenType.Star
                    If rpnStack.Count >= 2 Then
                        Dim b = rpnStack.Pop()
                        Dim a = rpnStack.Pop()
                        rpnStack.Push(a * b)
                    End If
                Case Token.TokenType.Power
                    If rpnStack.Count >= 2 Then
                        Dim b = rpnStack.Pop()
                        Dim a = rpnStack.Pop()
                        rpnStack.Push(Math.Pow(a, b))
                    End If
                Case Token.TokenType.UnaryMinus
                    If rpnStack.Count >= 1 Then
                        Dim a = rpnStack.Pop()
                        rpnStack.Push(-a)
                    End If
            End Select
        End While
        If Not rpnStack.Count = 1 Then
            point.Y = Double.NaN
        Else
            point.Y = rpnStack.Pop()
        End If
    End Function

    Public Sub pushOperator(ByRef operatorStack As Stack(Of Token), ByRef outputQueue As Queue(Of Token), ByRef token As Token)
        While operatorStack.Count > 0
            If (associativityLeft(token) And precedence(token) <= precedence(operatorStack.Peek())) Or
                (Not associativityLeft(token) And precedence(token) < precedence(operatorStack.Peek())) Then
                outputQueue.Enqueue(operatorStack.Pop())
            Else
                Exit While
            End If
        End While
        operatorStack.Push(token)
    End Sub

    Private Sub pictureBox1_Paint(ByVal sender As Object, ByVal e As System.Windows.Forms.PaintEventArgs) Handles PlotDisplay.Paint
        Dim g As Graphics = e.Graphics
        clearAndDrawAxis(g)
    End Sub

    Public Sub formClear() Handles MyBase.Resize, PlotDisplay.Click
        clearAndDrawAxis(PlotDisplay.CreateGraphics())
    End Sub

    Public Sub clearAndDrawAxis(ByRef graphics As Graphics)
        graphics.Clear(Color.FromArgb(255, 240, 240, 240))
        Dim pen As New Pen(Color.Red)
        graphics.DrawLine(pen, New PointF(PlotDisplay.Width / 2, 0), New PointF(PlotDisplay.Width / 2, PlotDisplay.Height))
        graphics.DrawLine(pen, New PointF(0, PlotDisplay.Height / 2), New PointF(PlotDisplay.Width, PlotDisplay.Height / 2))
    End Sub

    Public Function yIsValid(ByVal point As Point2D, ByVal rect As RectangleF) As Boolean
        If point.Y <= rect.Bottom And point.Y >= rect.Top Then
            Return True
        End If

        Return False
    End Function

    Public Sub finalizeFunction(ByRef functions As Stack(Of Queue(Of Token)), ByRef operatorStack As Stack(Of Token),
                                ByRef outputQueue As Queue(Of Token))
        While operatorStack.Count > 0
            Dim token = operatorStack.Pop()
            If token.type = Token.TokenType.LeftParenthesis Then
                MathInput.BackColor = Color.Red
                Return
            End If
            outputQueue.Enqueue(token)
        End While

        functions.Push(outputQueue)
        outputQueue = New Queue(Of Token)
    End Sub

    Public Sub handleInput()
        MathInput.BackColor = Color.Empty

        Dim tokenizer As Tokenizer = New Tokenizer
        With tokenizer
            .index = 0
            .stream = MathInput.Text
        End With

        Dim functions As New Stack(Of Queue(Of Token))
        Dim operatorStack As New Stack(Of Token)
        Dim outputQueue As New Queue(Of Token)
        Dim prevToken As Token = Nothing
        Dim prevExists As Boolean = False
        While True
            Dim token As Token = getToken(tokenizer)

            ' NOTE(nox): Shunting yard algorithm
            Select Case token.type
                Case Token.TokenType.EndToken
                    finalizeFunction(functions, operatorStack, outputQueue)
                    Exit While
                Case Token.TokenType.Unknown
                    MathInput.BackColor = Color.Red
                    Return

                Case Token.TokenType.Number, Token.TokenType.Variable
                    If prevExists Then
                        If prevToken.type = Token.TokenType.Number Or prevToken.type = Token.TokenType.RightParenthesis Or
                            prevToken.type = Token.TokenType.Variable Then
                            Dim starToken As Token = New Token
                            starToken.type = Token.TokenType.Star
                            pushOperator(operatorStack, outputQueue, starToken)
                        End If
                    End If

                    outputQueue.Enqueue(token)

                Case Token.TokenType.Minus
                    If prevExists Then
                        If prevToken.type = Token.TokenType.Number Or prevToken.type = Token.TokenType.RightParenthesis Or
                            prevToken.type = Token.TokenType.Variable Then
                            pushOperator(operatorStack, outputQueue, token)
                        Else
                            GoTo Unary
                        End If
                    Else
Unary:
                        token.type = Token.TokenType.UnaryMinus
                        pushOperator(operatorStack, outputQueue, token)
                    End If

                Case Token.TokenType.Plus, Token.TokenType.Slash, Token.TokenType.Star, Token.TokenType.Power
                    pushOperator(operatorStack, outputQueue, token)

                Case Token.TokenType.Identifier, Token.TokenType.LeftParenthesis
                    If prevExists Then
                        If prevToken.type = Token.TokenType.Number Or prevToken.type = Token.TokenType.RightParenthesis Or
                            prevToken.type = Token.TokenType.Variable Then
                            Dim starToken As Token = New Token
                            starToken.type = Token.TokenType.Star
                            pushOperator(operatorStack, outputQueue, starToken)
                        End If
                    End If

                    operatorStack.Push(token)

                Case Token.TokenType.RightParenthesis
                    While (Not operatorStack.Peek().type = Token.TokenType.LeftParenthesis)
                        outputQueue.Enqueue(operatorStack.Pop())
                        If operatorStack.Count = 0 Then
                            MathInput.BackColor = Color.Red
                            Return
                        End If
                    End While
                    operatorStack.Pop()
                    If operatorStack.Count > 0 Then
                        If operatorStack.Peek().type = Token.TokenType.Identifier Then
                            outputQueue.Enqueue(operatorStack.Pop())
                        End If
                    End If

                Case Token.TokenType.Comma
                    While operatorStack.Count > 0
                        If operatorStack.Peek().type = Token.TokenType.LeftParenthesis Then
                            Exit While
                        Else
                            outputQueue.Enqueue(operatorStack.Pop())
                        End If
                    End While
                    If operatorStack.Count = 0 Then
                        finalizeFunction(functions, operatorStack, outputQueue)
                        prevExists = False
                    End If
            End Select
            prevToken = token
            prevExists = True
        End While

        For Each queue In functions
            Dim n As Double = 1100
            Dim min As New Point2D(-10, -10)
            Dim max As New Point2D(10, 10)
            Dim range As New Point2D(max.X - min.X, max.Y - min.Y)
            Dim increment As Double = range.X / n
            Dim graphics As Graphics = PlotDisplay.CreateGraphics()
            Dim pen As New Pen(Color.Blue)
            Dim prev As New Point2D(min.X, 0)
            calculateResult(queue, prev)
            Dim prevValid As Boolean = yIsValid(prev, graphics.ClipBounds)
            For i As Integer = 1 To n
                Dim point As New Point2D(prev.X + increment, 0)
                calculateResult(queue, point)
                Dim valid As Boolean = yIsValid(point, graphics.ClipBounds)

                If valid And prevValid Then
                    graphics.DrawLine(pen,
                                      New PointF(PlotDisplay.Width / 2 + prev.X / range.X * PlotDisplay.Width,
                                                 PlotDisplay.Height / 2 - prev.Y / range.Y * PlotDisplay.Height),
                                      New PointF(PlotDisplay.Width / 2 + point.X / range.X * PlotDisplay.Width,
                                                 PlotDisplay.Height / 2 - point.Y / range.Y * PlotDisplay.Height))
                End If
                If valid And Not prevValid Then
                    Dim iterMax = 500
                    For iter = 1 To iterMax - 1
                        prev.X = prev.X + (point.X - prev.X) * iter / iterMax
                        calculateResult(queue, prev)
                        If yIsValid(prev, graphics.ClipBounds) Then
                            graphics.DrawLine(pen,
                                              New PointF(PlotDisplay.Width / 2 + prev.X / range.X * PlotDisplay.Width,
                                                         PlotDisplay.Height / 2 - prev.Y / range.Y * PlotDisplay.Height),
                                              New PointF(PlotDisplay.Width / 2 + point.X / range.X * PlotDisplay.Width,
                                                         PlotDisplay.Height / 2 - point.Y / range.Y * PlotDisplay.Height))
                            Exit For
                        End If
                    Next
                ElseIf Not valid And prevValid Then
                    Dim iterMax = 500
                    For iter = 1 To iterMax - 1
                        point.X = point.X + (prev.X - point.X) * iter / iterMax
                        calculateResult(queue, point)
                        If yIsValid(point, graphics.ClipBounds) Then
                            graphics.DrawLine(pen,
                                              New PointF(PlotDisplay.Width / 2 + prev.X / range.X * PlotDisplay.Width,
                                                         PlotDisplay.Height / 2 - prev.Y / range.Y * PlotDisplay.Height),
                                              New PointF(PlotDisplay.Width / 2 + point.X / range.X * PlotDisplay.Width,
                                                         PlotDisplay.Height / 2 - point.Y / range.Y * PlotDisplay.Height))
                            Exit For
                        End If
                    Next
                End If

                prev = point
                prevValid = valid
            Next
        Next
    End Sub
End Class
