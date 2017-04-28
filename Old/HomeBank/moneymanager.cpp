#include "moneymanager.h"
#include "dialogxmlcorrupted.h"
#include <QFile>
#include <QtXml>
#include <QDebug>


MoneyManager::MoneyManager()
{
    money = 0;
    moneyStr = "";
    corrupted = 0;

    // SET DEFAULT NOTES QUANTITIES //
    notesFive = 0;
    notesTen = 0;
    notesTwenty = 0;
    notesFifty = 0;
    notesHundred = 0;
    notesTHundred = 0;
    notesFHundred = 0;

    // APPEND NOTES VALUES //
    notesValues.append(5);
    notesValues.append(10);
    notesValues.append(20);
    notesValues.append(50);
    notesValues.append(100);
    notesValues.append(200);
    notesValues.append(500);

    update();

    if(!QDir("C:/Users/NoX/AppData/Roaming/Bank").exists())
    {
        QDir().mkdir("C:/Users/NoX/AppData/Roaming/Bank");
        saveFile();
    }

    QFile file("C:/Users/NoX/AppData/Roaming/Bank/money.xml");
    if (!file.open(QIODevice::ReadOnly))
    {
        qDebug() << "Error opening file, creating new one";
        saveFile();

    }


    if (!doc.setContent(&file))
    {
        qDebug() << "Error setting content, creating new one";
        file.close();
        saveFile();
    }


    docElem = doc.documentElement();

    notesList = docElem.elementsByTagName("money").at(0).toElement().elementsByTagName("notes").at(0).toElement().elementsByTagName("note");

    reasonsList = docElem.elementsByTagName("reasons").at(0).toElement().elementsByTagName("reason");

    for(int i = 0; i < reasonsList.count(); i++)
    {
        reasons.append(reasonsList.at(i).attributes().namedItem("reason").nodeValue());
    }

    getMoney();

}

bool MoneyManager::updateNotes(int five, int ten, int twenty, int fifty, int hundred, int tHundred, int fHundred)
{
    notesFive += five;
    notesTen += ten;
    notesTwenty += twenty;
    notesFifty += fifty;
    notesHundred += hundred;
    notesTHundred += tHundred;
    notesFHundred += fHundred;

    if(notesFive  < 0 || notesTen  < 0 || notesTwenty < 0 || notesFifty < 0 || notesHundred < 0 || notesTHundred < 0 || notesFHundred < 0)
    {
        qDebug() << "You don't have enough money to do that withdraw!";
        notesFive -= five;
        notesTen -= ten;
        notesTwenty -= twenty;
        notesFifty -= fifty;
        notesHundred -= hundred;
        notesTHundred -= tHundred;
        notesFHundred -= fHundred;
        return 0;
    }

    sumMoney();
    return 1;

}

void MoneyManager::update()
{
    // UPDATE THE QLIST OF THE NOTES //
    notes.clear();

    // APPEND NUMBER OF NOTES //
    notes.append(notesFive);
    notes.append(notesTen);
    notes.append(notesTwenty);
    notes.append(notesFifty);
    notes.append(notesHundred);
    notes.append(notesTHundred);
    notes.append(notesFHundred);
}

void MoneyManager::update(QString reason)
{
    update();
    reasons.append(reason);
}

void MoneyManager::getMoney()
{
    if (notesList.count() == 7)
    {
        notesFive = notesList.at(0).attributes().namedItem("quant").nodeValue().toInt();
        notesTen = notesList.at(1).attributes().namedItem("quant").nodeValue().toInt();
        notesTwenty = notesList.at(2).attributes().namedItem("quant").nodeValue().toInt();
        notesFifty = notesList.at(3).attributes().namedItem("quant").nodeValue().toInt();
        notesHundred = notesList.at(4).attributes().namedItem("quant").nodeValue().toInt();
        notesTHundred = notesList.at(5).attributes().namedItem("quant").nodeValue().toInt();
        notesFHundred = notesList.at(6).attributes().namedItem("quant").nodeValue().toInt();

        money = docElem.elementsByTagName("money").at(0).toElement().elementsByTagName("total").at(0).attributes().namedItem("quant").nodeValue().toInt();

        if(money != 0)
            moneyStr = QString::number(money);
        else
            moneyStr = "--------";
    }

    else
    {
        qDebug() << "XML File Corrupted!";
        corrupted = 1;
    }

}

void MoneyManager::sumMoney()
{
    // NOTES //
    money = notesFive * notesList.at(0).attributes().namedItem("value").nodeValue().toInt(false, 10) + notesTen * notesList.at(1).attributes().namedItem("value").nodeValue().toInt(false, 10) + notesTwenty * notesList.at(2).attributes().namedItem("value").nodeValue().toInt(false, 10) + notesFifty * notesList.at(3).attributes().namedItem("value").nodeValue().toInt(false, 10) + notesHundred * notesList.at(4).attributes().namedItem("value").nodeValue().toInt(false, 10) + notesTHundred * notesList.at(5).attributes().namedItem("value").nodeValue().toInt(false, 10) + notesFHundred * notesList.at(6).attributes().namedItem("value").nodeValue().toInt(false, 10);
    // NOTES FINISHED //

    if(money != 0)
        moneyStr = QString::number(money);
    else
        moneyStr = "--------";
}

void MoneyManager::saveFile()
{
    QFile file("C:/Users/NoX/AppData/Roaming/Bank/money.xml");
    update();

    // CREATES BASIC NODES //
    QDomDocument document;
    QDomElement root = document.createElement("root");
    QDomElement info = document.createElement("info");
    QDomElement total = document.createElement("total");
    QDomElement instructions = document.createElement("instructions");
    QDomElement infoText = document.createElement("infoText");
    QDomElement money = document.createElement("money");
    QDomElement notesNode = document.createElement("notes");
    QDomElement reasonsNode = document.createElement("reasons");

    // APPENDS THEM TO THE DOCUMENT //
    document.appendChild(root); 
    root.appendChild(money);
    money.appendChild(notesNode);
    root.appendChild(reasonsNode);
    root.appendChild(info);
    info.appendChild(infoText);
    info.appendChild(instructions);
    infoText.setAttribute("info", "THIS FILE WAS AUTOMATICALLY CREATED BY HOMEBANK PROGRAM");
    infoText.setAttribute("about", "HOMEBANK WAS CREATED BY GONÇALO SANTOS (AKA. INOX)");
    instructions.setAttribute("instructions", "Please, use the program to change this file, unless you know what you are doing. You may corrupt it if you don't know.");

    money.appendChild(total);
    total.setAttribute("quant", this->money);

    // CREATES NOTES NODES //
    for(int i = 0; i < notes.count(); i++)
    {
        QDomElement noteNode = document.createElement("note");
        notesNode.appendChild(noteNode);
        noteNode.setAttribute("value", notesValues.at(i));
        noteNode.setAttribute("quant", notes.at(i));
    }

    // REASONS/LAST MOVEMENTS //
    for(int i = 0; i < reasons.count(); i++)
    {
        QDomElement reason = document.createElement("reason");
        reasonsNode.appendChild(reason);
        reason.setAttribute("reason", reasons.at(i));
    }

    // SAVES TO FILE //
    if(file.open(QIODevice::WriteOnly | QIODevice::Text))
    {
        QTextStream stream(&file);
        stream << document.toString();
        file.close();
        qDebug() << "File successfully saved";
    }
    else
    {
        qDebug() << "Error opening file!";
    }

}
