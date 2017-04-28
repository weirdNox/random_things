<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class Form1
    Inherits System.Windows.Forms.Form

    'Form overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()> _
    Protected Overrides Sub Dispose(ByVal disposing As Boolean)
        Try
            If disposing AndAlso components IsNot Nothing Then
                components.Dispose()
            End If
        Finally
            MyBase.Dispose(disposing)
        End Try
    End Sub

    'Required by the Windows Form Designer
    Private components As System.ComponentModel.IContainer

    'NOTE: The following procedure is required by the Windows Form Designer
    'It can be modified using the Windows Form Designer.  
    'Do not modify it using the code editor.
    <System.Diagnostics.DebuggerStepThrough()> _
    Private Sub InitializeComponent()
        Me.MathInput = New System.Windows.Forms.TextBox()
        Me.PlotDisplay = New System.Windows.Forms.PictureBox()
        Me.TableLayoutPanel1 = New System.Windows.Forms.TableLayoutPanel()
        CType(Me.PlotDisplay, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.TableLayoutPanel1.SuspendLayout()
        Me.SuspendLayout()
        '
        'MathInput
        '
        Me.MathInput.Dock = System.Windows.Forms.DockStyle.Fill
        Me.MathInput.Location = New System.Drawing.Point(3, 3)
        Me.MathInput.Name = "MathInput"
        Me.MathInput.Size = New System.Drawing.Size(278, 20)
        Me.MathInput.TabIndex = 0
        '
        'PlotDisplay
        '
        Me.PlotDisplay.Dock = System.Windows.Forms.DockStyle.Fill
        Me.PlotDisplay.Location = New System.Drawing.Point(3, 30)
        Me.PlotDisplay.Name = "PlotDisplay"
        Me.PlotDisplay.Size = New System.Drawing.Size(278, 228)
        Me.PlotDisplay.TabIndex = 1
        Me.PlotDisplay.TabStop = False
        '
        'TableLayoutPanel1
        '
        Me.TableLayoutPanel1.AutoSize = True
        Me.TableLayoutPanel1.ColumnCount = 1
        Me.TableLayoutPanel1.ColumnStyles.Add(New System.Windows.Forms.ColumnStyle(System.Windows.Forms.SizeType.Absolute, 284.0!))
        Me.TableLayoutPanel1.Controls.Add(Me.MathInput, 0, 0)
        Me.TableLayoutPanel1.Controls.Add(Me.PlotDisplay, 0, 1)
        Me.TableLayoutPanel1.Dock = System.Windows.Forms.DockStyle.Fill
        Me.TableLayoutPanel1.Location = New System.Drawing.Point(0, 0)
        Me.TableLayoutPanel1.Name = "TableLayoutPanel1"
        Me.TableLayoutPanel1.RowCount = 2
        Me.TableLayoutPanel1.RowStyles.Add(New System.Windows.Forms.RowStyle(System.Windows.Forms.SizeType.Absolute, 27.0!))
        Me.TableLayoutPanel1.RowStyles.Add(New System.Windows.Forms.RowStyle())
        Me.TableLayoutPanel1.Size = New System.Drawing.Size(284, 261)
        Me.TableLayoutPanel1.TabIndex = 2
        '
        'Form1
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(6.0!, 13.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(284, 261)
        Me.Controls.Add(Me.TableLayoutPanel1)
        Me.Name = "Form1"
        Me.Text = "Form1"
        CType(Me.PlotDisplay, System.ComponentModel.ISupportInitialize).EndInit()
        Me.TableLayoutPanel1.ResumeLayout(False)
        Me.TableLayoutPanel1.PerformLayout()
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub

    Friend WithEvents MathInput As TextBox
    Friend WithEvents PlotDisplay As PictureBox
    Friend WithEvents TableLayoutPanel1 As TableLayoutPanel
End Class
