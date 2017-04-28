#ifndef MAINWINDOW_H
#define MAINWINDOW_H
#include <QWidget>
#include <QMenu>
#include <QMenuBar>

class MoneyManager;

namespace Ui {
class mainwindow;
}

class MainWindow : public QWidget
{
    Q_OBJECT
    
public:
    explicit MainWindow(QWidget *parent = 0);
    ~MainWindow();
    void corrupted();

private slots:
    void on_updateButton_clicked();
    void saveFileSlot();

private:
    MoneyManager *moneyManager;
    Ui::mainwindow *ui;
    void updateMovements();
    void updateMoney();
    void resetSliders();

};

#endif // MAINWINDOW_H
