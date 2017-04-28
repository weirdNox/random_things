#include <QtGui/QApplication>
#include "mainwindow.h"

int main(int argc, char *argv[])
{
    QApplication a(argc, argv);
    MainWindow w;
    a.connect(&a, SIGNAL(lastWindowClosed()), &w, SLOT(saveFileSlot()));
    w.show();
    
    return a.exec();
}

