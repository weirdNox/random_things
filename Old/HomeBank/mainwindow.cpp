#include "mainwindow.h"
#include "ui_mainwindow.h"
#include "moneymanager.h"

MainWindow::MainWindow(QWidget *parent) :
    QWidget(parent),
    ui(new Ui::mainwindow)
{
    ui->setupUi(this);

    moneyManager = new MoneyManager();
    updateMovements();
    updateMoney();
    resetSliders();

    if(moneyManager->corrupted)
        corrupted();
}

MainWindow::~MainWindow()
{
    delete ui;
}

void MainWindow::corrupted()
{
    ui->corrupted->show();
    ui->updateButton->setEnabled(false);

    ui->noteFive->setText("-");
    ui->noteTen->setText("-");
    ui->noteTwenty->setText("-");
    ui->noteFifty->setText("-");
    ui->noteHundred->setText("-");
    ui->noteTHundred->setText("-");
    ui->noteFHundred->setText("-");
    ui->moneyQt->setText("CORRUPTED");
    ui->reasonLE->setPlaceholderText("CORRUPTED");
    ui->reasonLE->setEnabled(false);

    ui->fiveSlider->setEnabled(false);
    ui->tenSlider->setEnabled(false);
    ui->twentySlider->setEnabled(false);
    ui->fiftySlider->setEnabled(false);
    ui->hundredSlider->setEnabled(false);
    ui->tHundredSlider->setEnabled(false);
    ui->fHundredSlider->setEnabled(false);

    ui->fiveSpin->setEnabled(false);
    ui->tenSpin->setEnabled(false);
    ui->twentySpin->setEnabled(false);
    ui->fiftySpin->setEnabled(false);
    ui->hundredSpin->setEnabled(false);
    ui->tHundredSpin->setEnabled(false);
    ui->fHundredSpin->setEnabled(false);
}

void MainWindow::on_updateButton_clicked()
{
    // VERIFIES IF THERE IS SOMETHING TO ADD/REMOVE //
    if(ui->fiveSpin->value() == 0 && ui->tenSpin->value() == 0 && ui->twentySpin->value() == 0 && ui->fiftySpin->value() == 0 && ui->hundredSpin->value() == 0 && ui->tHundredSpin->value() == 0 && ui->fHundredSpin->value() == 0)
        return;

    else
    {
        // DEPOSITS OR WITHDRAWS MONEY //
        if(moneyManager->updateNotes(ui->fiveSpin->value(), ui->tenSpin->value(), ui->twentySpin->value(), ui->fiftySpin->value(), ui->hundredSpin->value(), ui->tHundredSpin->value(), ui->fHundredSpin->value()))
        {
            // CREATES REASON STRING //
            QString reason = ui->reasonLE->text();
            // CREATES MOVEMENT INT //
            int movement = ui->fiveSlider->value() * 5 + ui->tenSpin->value() * 10 + ui->twentySpin->value() * 20 + ui->fiftySpin->value() * 50 + ui->hundredSpin->value() * 100 + ui->tHundredSlider->value() * 200 + ui->fHundredSpin->value() * 500;

            // VERIFIES REASON //
            if(reason.length() == 0)
            {
                if(movement > 0)
                    reason = "+" + QString::number(movement);
                else
                    reason = QString::number(movement);
            }
            else
            {
                if(movement > 0)
                    reason = reason + ", +" + QString::number(movement);
                else
                    reason = reason + ", " + QString::number(movement);
            }

            QDate date = QDate::currentDate();
            reason = reason + ", on " + date.toString();

            // UPDATES EVERYTHING //
            moneyManager->update(reason);
            moneyManager->saveFile();
            updateMovements();
            updateMoney();
            resetSliders();
        }
        else
            ui->enoughMoney->show();
    }

}

void MainWindow::saveFileSlot()
{
    moneyManager->saveFile();
}

void MainWindow::updateMovements()
{
    if(moneyManager->reasons.count() >= 7)
    {
        ui->reason0->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 1));
        ui->reason1->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 1 - 1));
        ui->reason2->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 2 - 1));
        ui->reason3->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 3 - 1));
        ui->reason4->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 4 - 1));
        ui->reason5->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 5 - 1));
        ui->reason6->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 6 - 1));
        ui->moveTitle->setText("Last 7 movements");
    }
    else
    {
        if(moneyManager->reasons.count() >= 1)
        {
            ui->reason0->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 1));
            ui->moveTitle->setText("Last movement");
        }
        if(moneyManager->reasons.count() >= 2)
        {
            ui->reason1->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 1 - 1));
            ui->moveTitle->setText("Last 2 movements");
        }
        if(moneyManager->reasons.count() >= 3)
        {
            ui->reason2->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 2 - 1));
            ui->moveTitle->setText("Last 3 movements");
        }
        if(moneyManager->reasons.count() >= 4)
        {
            ui->reason3->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 3 - 1));
            ui->moveTitle->setText("Last 4 movements");
        }
        if(moneyManager->reasons.count() >= 5)
        {
            ui->reason4->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 4 - 1));
            ui->moveTitle->setText("Last 5 movements");
        }
        if(moneyManager->reasons.count() == 6)
        {
            ui->reason5->setText(moneyManager->reasons.at(moneyManager->reasons.count() - 5 - 1));
            ui->moveTitle->setText("Last 6 movements");
        }

        if(ui->reason0->text() == "REASON")
        {
            ui->reason0->clear();
            ui->moveTitle->setText("No movements to show");
        }
        if(ui->reason1->text() == "REASON")
            ui->reason1->clear();
        if(ui->reason2->text() == "REASON")
            ui->reason2->clear();
        if(ui->reason3->text() == "REASON")
            ui->reason3->clear();
        if(ui->reason4->text() == "REASON")
            ui->reason4->clear();
        if(ui->reason5->text() == "REASON")
            ui->reason5->clear();
        if(ui->reason6->text() == "REASON")
            ui->reason6->clear();
    }
}

void MainWindow::updateMoney()
{
    ui->moneyQt->setText(moneyManager->moneyStr);

    ui->enoughMoney->hide();
    ui->corrupted->hide();

    // INFORMATIONS //
    ui->noteFive->setText(QString::number(moneyManager->notesFive));
    ui->noteTen->setText(QString::number(moneyManager->notesTen));
    ui->noteTwenty->setText(QString::number(moneyManager->notesTwenty));
    ui->noteFifty->setText(QString::number(moneyManager->notesFifty));
    ui->noteHundred->setText(QString::number(moneyManager->notesHundred));
    ui->noteTHundred->setText(QString::number(moneyManager->notesTHundred));
    ui->noteFHundred->setText(QString::number(moneyManager->notesFHundred));
    ui->reasonLE->clear();
}

void MainWindow::resetSliders()
{
    ui->fiveSlider->setValue(0);
    ui->fiveSpin->setValue(0);
    ui->tenSlider->setValue(0);
    ui->tenSpin->setValue(0);
    ui->twentySlider->setValue(0);
    ui->twentySpin->setValue(0);
    ui->fiftySlider->setValue(0);
    ui->fiftySpin->setValue(0);
    ui->hundredSlider->setValue(0);
    ui->hundredSpin->setValue(0);
    ui->tHundredSlider->setValue(0);
    ui->tHundredSpin->setValue(0);
    ui->fHundredSlider->setValue(0);
    ui->fHundredSpin->setValue(0);
}
