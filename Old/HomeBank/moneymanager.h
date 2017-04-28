#ifndef MONEYMANAGER_H
#define MONEYMANAGER_H
#include <QWidget>
#include <QtXml>
#include "mainwindow.h"

class MoneyManager
{
public:
    explicit MoneyManager();
    ~MoneyManager();
    QString moneyStr;
    float money;
    bool updateNotes(int five, int ten, int twenty, int fifty, int hundred, int tHundred, int fHundred);
    QList<int> notes;
    QList<int> notesValues;

    // NOTES
    int notesFive;
    int notesTen;
    int notesTwenty;
    int notesFifty;
    int notesHundred;
    int notesTHundred;
    int notesFHundred;

    void update();
    void update(QString reason);
    QList<QString> reasons;
    void saveFile();
    bool corrupted;

private:
    void getMoney();
    void sumMoney();
    QDomNodeList notesList;
    QDomNodeList coinsList;
    QDomNodeList reasonsList;
    QDomDocument doc;
    QDomElement docElem;
};

#endif // MONEYMANAGER_H
