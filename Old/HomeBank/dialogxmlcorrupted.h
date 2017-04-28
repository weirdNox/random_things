#ifndef DIALOGXMLCORRUPTED_H
#define DIALOGXMLCORRUPTED_H

#include <QDialog>
#include "moneymanager.h"

namespace Ui {
class DialogXMLCorrupted;
}

class DialogXMLCorrupted : public QDialog
{
    Q_OBJECT
    
public:
    explicit DialogXMLCorrupted(QWidget *parent = 0);
    ~DialogXMLCorrupted();
    

private slots:
    void on_pushButton_released();

private:
    Ui::DialogXMLCorrupted *ui;
};

#endif // DIALOGXMLCORRUPTED_H
