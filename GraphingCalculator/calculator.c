/*
  === Calculator and function plotter ===

  This is  a calculator that is  able to parse multiple  mathematical expressions, convert
  them to  postfix notation and  then calculate  the result (in  a deferred way).   It has
  support for several math functions (and adding more is very simple!).

  The most satisfying feature  in this program is the ability to  plot functions! Just set
  your window  and write some functions  with the x variable  and it will create  an image
  with those functions plotted.

  Disclaimer: This program was made iteratively, and  I wanted to add some features in the
  end that  I hadn't thought of  in the beginning, so  I have lots of  workarounds and, in
  general, it  is a mess...  I may clean  it up eventually, but,  for now, _YOU  HAVE BEEN
  WARNED_!


  --- Usage ---
  You can write expressions with the following operators:
    +  sum
    -  subtract (or unary minus)
    *  multiply
    /  divide
    ^  power
    () to change the order of operation or write function arguments
    ,  for functions requiring several arguments or to write several expressions at once

  These functions are available:
    sin(angle)
    cos(angle)
    tan(angle)
    sqrt(number)
    ln(number)
    log(base, number)
    abs(number)

  These constants and variables are available:
    x - variable
    pi
    e

  This calculator also has some internal commands to set options, like:
    setval - set value of variable x (used when graphing mode is disabled)
    graph - enable graphing mode
    nograph - disable graphing mode
    color - set graphing colors (or, if every color is 0, use the default)
    exit - end the program

  As an example, you can try plotting the batman curve:
    - Enable graphing mode with x axis [-7, 7] and y axis [-5, 5]
    - Insert this expression 2*sqrt(-abs(abs(x)-1)*abs(3-abs(x))/((abs(x)-1)*(3-abs(x))))(1+abs(abs(x)-3)/(abs(x)-3))sqrt(1-(x/7)^2)+(5+0.97(abs(x-.5)+abs(x+.5))-3(abs(x-.75)+abs(x+.75)))(1+abs(1-abs(x))/(1-abs(x))),-3sqrt(1-(x/7)^2)sqrt(abs(abs(x)-4)/(abs(x)-4)),abs(x/2)-0.0913722(x^2)-3+sqrt(1-(abs(abs(x)-2)-1)^2),(2.71052+(1.5-.5abs(x))-1.35526sqrt(4-(abs(x)-1)^2))sqrt(abs(abs(x)-1)/(abs(x)-1))+0.9
    - View output.png

  Enjoy!

 */

#define STB_IMAGE_WRITE_IMPLEMENTATION
#include "stb_image_write.h"
#include <ctype.h>
#include <math.h>
#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>
#include <string.h>

#define arrayCount(array) (sizeof(array)/sizeof(array[0]))
typedef char bool;

typedef enum
{
    Token_Number,

    Token_Plus,
    Token_UnaryMinus,
    Token_BinaryMinus,
    Token_Star,
    Token_Slash,
    Token_Circumflex,
    Token_Comma,

    Token_OpenParen,
    Token_CloseParen,

    Token_Function,
    Token_Identifier,

    Token_EOF,
    Token_Keyword,
    Token_Unknown,
} TokenType;

typedef enum
{
    Function_Unknown,

    Function_Sin,
    Function_Cos,
    Function_Tan,
    Function_Sqrt,
    Function_Ln,
    Function_Log,
    Function_Abs,
} MathFunction;

typedef enum
{
    Associativity_Left,
    Associativity_Right
} Associativity;

typedef struct
{
    TokenType type;

    char *text;
    int size;
    double number;
} Token;

typedef struct
{
    char *at;
    Token prevToken;
} Tokenizer;

typedef struct
{
    bool shouldExit;
    double variableValue;
    bool graphing;
    double minX, maxX;
    double minY, maxY;
    bool definedGraphColor;
    int graphColor[3];
    bool isDeg; // TODO(nox): Implement this!
} ExpressionParserOptions;

typedef struct
{
    char *text;
    int textLength;
    int size;
    Token queue[1<<9];
    int failed;
} TokenQueue;

typedef struct
{
    int numExpressions;
    TokenQueue queues[15];
} ExpressionsResult;

typedef struct
{
    int width;
    int height;
    int stride;
    uint8_t *data;
} Image;

int graphColors[][3] = {{ 57, 106, 177},
                        {218, 124,  48},
                        { 62, 150,  81},
                        {204,  37,  41},
                        {107,  76, 154},
                        {255,   0, 255},
                        {  0, 255, 255},
                        {255,   0,   0},
                        {  0, 255,   0},
                        {  0,   0, 255}};

Associativity getAssociativity(TokenType type)
{
    switch(type)
    {
        case Token_Circumflex:
        {
            return Associativity_Right;
        } break;

        default:
        {
            return Associativity_Left;
        } break;
    }
}

int getPrecedence(TokenType type)
{
    switch(type)
    {
        case Token_Circumflex:
        case Token_UnaryMinus:
        {
            return 3;
        } break;

        case Token_Star:
        case Token_Slash:
        {
            return 2;
        } break;

        default:
        {
            return 1;
        } break;
    }
}

bool isDigit(char c)
{
    return (c >= '0' && c <= '9');
}

bool isAlpha(char c)
{
    return ((c >= 'a' && c <= 'z') ||
            (c >= 'A' && c <= 'Z'));
}

void eatWhitespace(Tokenizer *tokenizer)
{
    for(;;)
    {
        char c = tokenizer->at[0];
        if(c == ' ' || c == '\t' || c == '\v' || c == '\v')
        {
            ++(tokenizer->at);
        }
        else
        {
            break;
        }
    }
}

bool previousIsNumber(Token prevToken)
{
    return (prevToken.type == Token_Number ||
            prevToken.type == Token_CloseParen ||
            prevToken.type == Token_Identifier);
}

void readInteger(char *prompt, int *number)
{
    for(;;)
    {
        char buffer[1<<10] = {0};
        printf("%s", prompt);
        fgets(buffer, arrayCount(buffer), stdin);
        if(sscanf(buffer, "%d", number) == 1)
        {
            break;
        }
    }
}

void readReal(char *prompt, double *number)
{
    for(;;)
    {
        char buffer[1<<10] = {0};
        printf("%s", prompt);
        fgets(buffer, arrayCount(buffer), stdin);
        if(sscanf(buffer, "%lf", number) == 1)
        {
            break;
        }
    }
}

void tokenTextToBuffer(Token token, char output[])
{
    memcpy(output, token.text, token.size);
    output[token.size] = '\0';

    for(int i = 0; i < token.size; ++i)
    {
        output[i] = (char)tolower(token.text[i]);
    }
}

MathFunction getFunctionFromToken(Token token)
{
    char name[1<<10];
    tokenTextToBuffer(token, name);

    if(strcmp(name, "sin") == 0)
    {
        return Function_Sin;
    }
    else if(strcmp(name, "cos") == 0)
    {
        return Function_Cos;
    }
    else if(strcmp(name, "tan") == 0)
    {
        return Function_Tan;
    }
    else if(strcmp(name, "sqrt") == 0)
    {
        return Function_Sqrt;
    }
    else if(strcmp(name, "ln") == 0)
    {
        return Function_Ln;
    }
    else if(strcmp(name, "log") == 0)
    {
        return Function_Log;
    }
    else if(strcmp(name, "abs") == 0)
    {
        return Function_Abs;
    }

    return Function_Unknown;
}

Token parseToken(Tokenizer *tokenizer, ExpressionParserOptions *options)
{
    eatWhitespace(tokenizer);

    Token token = {0};
    token.type = Token_Unknown;
    token.text = tokenizer->at;
    token.size = 1;

    char c = tokenizer->at[0];
    ++tokenizer->at;
    switch(c)
    {
        case '\n': {token.type = Token_EOF;} break;

        case '+': {token.type = Token_Plus;} break;
        case '*': {token.type = Token_Star;} break;
        case '/': {token.type = Token_Slash;} break;
        case '^': {token.type = Token_Circumflex;} break;
        case '(': {token.type = Token_OpenParen;} break;
        case ')': {token.type = Token_CloseParen;} break;
        case ',': {token.type = Token_Comma;} break;

        case '-':
        {
            if(previousIsNumber(tokenizer->prevToken))
            {
                token.type = Token_BinaryMinus;
            }
            else
            {
                token.type = Token_UnaryMinus;
            }
        } break;

        default:
        {
            if(isDigit(c) || c == '.')
            {
                token.type = Token_Number;
                while(isDigit(tokenizer->at[0]) || tokenizer->at[0] == '.')
                {
                    ++tokenizer->at;
                }
                token.size = (int)(tokenizer->at - token.text);
                sscanf(token.text, "%lf", &token.number);
            }
            else if(isAlpha(c))
            {
                token.type = Token_Function;
                while(isAlpha(tokenizer->at[0]) || isDigit(tokenizer->at[0]))
                {
                    ++tokenizer->at;
                }
                token.size = (int)(tokenizer->at - token.text);

                // NOTE(nox): Check for special keywords
                char name[1<<10];
                tokenTextToBuffer(token, name);

                // NOTE(nox): Variable
                if(strcmp(name, "x") == 0)
                {
                    token.type = Token_Identifier;
                }
                // NOTE(nox): Constants
                else if(strcmp(name, "pi") == 0)
                {
                    token.type = Token_Number;
                    token.number = 3.14159265359;
                }
                else if(strcmp(name, "e") == 0)
                {
                    token.type = Token_Number;
                    token.number = 2.7182818284;
                }
                // NOTE(nox): Keywords
                else if(strcmp(name, "exit") == 0)
                {
                    token.type = Token_Keyword;
                    options->shouldExit = 1;
                }
                else if(strcmp(name, "setval") == 0)
                {
                    token.type = Token_Keyword;
                    readReal("Value for x: ", &options->variableValue);
                }
                else if(strcmp(name, "nograph") == 0)
                {
                    token.type = Token_Keyword;
                    options->graphing = 0;
                }
                else if(strcmp(name, "graph") == 0)
                {
                    token.type = Token_Keyword;
                    options->graphing = 1;
                    readReal("Minimum x: ", &options->minX);
                    readReal("Maximum x: ", &options->maxX);
                    readReal("Minimum y: ", &options->minY);
                    readReal("Maximum y: ", &options->maxY);

                    if(options->minX >= options->maxX || options->minY >= options->maxY)
                    {
                        options->graphing = 0;
                        puts("Graphing function arguments are not correct.");
                    }
                }
                else if(strcmp(name, "color") == 0)
                {
                    token.type = Token_Keyword;
                    options->definedGraphColor = 1;

                    readInteger("Red (0-255): ", &options->graphColor[0]);
                    readInteger("Green (0-255): ", &options->graphColor[1]);
                    readInteger("Blue (0-255): ", &options->graphColor[2]);

                    if(options->graphColor[0] < 0 || options->graphColor[0] > 255 ||
                       options->graphColor[1] < 0 || options->graphColor[1] > 255 ||
                       options->graphColor[2] < 0 || options->graphColor[2] > 255)
                    {
                        options->definedGraphColor = 0;
                        puts("Invalid graph colors.");
                    }
                    else if(options->graphColor[0] == 0 &&
                            options->graphColor[1] == 0 &&
                            options->graphColor[2] == 0)
                    {
                        options->definedGraphColor = 0;
                        puts("User defined graph color disabled.");
                    }
                    else
                    {
                        options->definedGraphColor = 1;
                        puts("User defined graph color enabled.");
                    }
                }
            }
        } break;
    }

    return token;
}

void endExpression(TokenQueue *output, int *stackSize, Token stack[],
                   char *lastChar)
{
    while(*stackSize)
    {
        Token newToken = stack[--(*stackSize)];
        if(newToken.type == Token_OpenParen)
        {
            output->failed = 1;
            *stackSize = 0;
            break;
        }

        output->queue[output->size++] = newToken;
    }

    output->textLength = lastChar - output->text;
}

bool addToken(Token token, ExpressionsResult *result, int *stackSize, Token stack[],
              Tokenizer *tokenizer, bool *parsing)
{
    TokenQueue *output = result->queues + result->numExpressions - 1;
    switch(token.type)
    {
        case Token_EOF:
        {
            endExpression(output, stackSize, stack, tokenizer->at - token.size);
            *parsing = 0;
        } break;

        case Token_Unknown: {} break;

        case Token_Number:
        case Token_Identifier:
        {
            if(previousIsNumber(tokenizer->prevToken))
            {
                Token implicitToken = {0};
                implicitToken.type = Token_Star;
                addToken(implicitToken, result, stackSize, stack, tokenizer,
                         parsing);
            }

            output->queue[output->size++] = token;
        } break;

        case Token_OpenParen:
        case Token_Function:
        {
            if(previousIsNumber(tokenizer->prevToken))
            {
                Token implicitToken = {0};
                implicitToken.type = Token_Star;
                addToken(implicitToken, result, stackSize, stack, tokenizer,
                         parsing);
            }

            stack[(*stackSize)++] = token;
        } break;

        case Token_CloseParen:
        {
            bool found = 0;
            while((*stackSize))
            {
                Token operator = stack[--(*stackSize)];
                if(operator.type != Token_OpenParen)
                {
                    output->queue[output->size++] = operator;
                }
                else
                {
                    found = 1;
                    break;
                }
            }

            if(!found)
            {
                output->failed = 1;
            }

            if(*stackSize)
            {
                Token testToken = stack[*stackSize-1];
                if(testToken.type == Token_Function)
                {
                    output->queue[output->size++] = testToken;
                    --(*stackSize);
                }
            }
        } break;

        case Token_Comma:
        {
            bool found = 0;
            int tempSize = *stackSize;
            while(tempSize)
            {
                Token *operator = stack + --tempSize;
                if(operator->type == Token_OpenParen)
                {
                    found = 1;
                    break;
                }
            }

            if(!found)
            {
                // NOTE(nox): New expression
                endExpression(output, stackSize, stack, tokenizer->at - token.size);

                eatWhitespace(tokenizer);
                result->queues[result->numExpressions++].text = tokenizer->at;
            }
        } break;

        case Token_Keyword:
        {
            output->size = 0;
            return 0;
        } break;

        default:
        {
            while(*stackSize)
            {
                Token operator = stack[*stackSize - 1];
                int tokenPrecedence = getPrecedence(token.type);
                int operatorPrecedence = getPrecedence(operator.type);

                if(operator.type != Token_OpenParen &&
                   ((getAssociativity(token.type) == Associativity_Left && tokenPrecedence <= operatorPrecedence) ||
                    (getAssociativity(token.type) == Associativity_Right && tokenPrecedence < operatorPrecedence)))
                {
                    output->queue[output->size++] = operator;
                    --*stackSize;
                }
                else
                {
                    break;
                }
            }
            stack[(*stackSize)++] = token;
        } break;
    }

    return 1;
}

bool parseExpression(char buffer[], ExpressionsResult *result, ExpressionParserOptions *options)
{
    int operatorStackSize = 0;
    Token operatorStack[1<<10];

    Tokenizer tokenizer;
    tokenizer.at = buffer;
    tokenizer.prevToken.type = Token_Unknown;

    result->numExpressions = 1;

    eatWhitespace(&tokenizer);
    result->queues[0].text = tokenizer.at;

    bool parsing = 1;
    while(parsing)
    {
        // TODO(nox): We are currently not checking if the number of tokens is bigger than
        // the maximum stack and output size!
        Token token = parseToken(&tokenizer, options);

        if(tokenizer.prevToken.type == Token_Function &&
           token.type != Token_OpenParen)
        {
            result->queues[result->numExpressions-1].failed = 1;
        }

        if(!addToken(token, result, &operatorStackSize, operatorStack, &tokenizer,
                     &parsing))
        {
            return 0;
        }

        tokenizer.prevToken = token;
    }

    for(int i = 0; i < result->numExpressions; ++i)
    {
        TokenQueue *queue = result->queues + i;
        if(queue->failed)
        {
            char buffer[1<<8] = {0};
            memcpy(buffer, queue->text, queue->textLength);
            printf("Malformed expression: %s\n", buffer);
        }
    }

    return 1;
}

double solveParsedExpression(TokenQueue *parsed, ExpressionParserOptions *options)
{
    if(parsed->failed)
    {
        return NAN;
    }

    int workingStackSize = 0;
    double workingStack[1<<10] = {0};
    for(int queueIndex = 0; queueIndex < parsed->size; ++queueIndex)
    {
        Token *nextToken = parsed->queue + queueIndex;
        switch(nextToken->type)
        {
            case Token_Number:
            {
                workingStack[workingStackSize++] = nextToken->number;
            } break;

            case Token_Identifier:
            {
                workingStack[workingStackSize++] = options->variableValue;
            } break;

            case Token_Plus:
            {
                if(workingStackSize < 2)
                {
                    parsed->failed = 1;
                    break;
                }

                double number2 = workingStack[--workingStackSize];
                double number1 = workingStack[--workingStackSize];
                workingStack[workingStackSize++] = number1+number2;
            } break;

            case Token_UnaryMinus:
            {
                if(workingStackSize < 1)
                {
                    parsed->failed = 1;
                    break;
                }

                double number = workingStack[--workingStackSize];
                workingStack[workingStackSize++] = -number;
            } break;

            case Token_BinaryMinus:
            {
                if(workingStackSize < 2)
                {
                    parsed->failed = 1;
                    break;
                }

                double number2 = workingStack[--workingStackSize];
                double number1 = workingStack[--workingStackSize];
                workingStack[workingStackSize++] = number1-number2;
            } break;

            case Token_Star:
            {
                if(workingStackSize < 2)
                {
                    parsed->failed = 1;
                    break;
                }

                double number2 = workingStack[--workingStackSize];
                double number1 = workingStack[--workingStackSize];
                workingStack[workingStackSize++] = number1*number2;
            } break;

            case Token_Slash:
            {
                if(workingStackSize < 2)
                {
                    parsed->failed = 1;
                    break;
                }

                double number2 = workingStack[--workingStackSize];
                double number1 = workingStack[--workingStackSize];

                if(number2 == 0.0)
                {
                    workingStack[workingStackSize++] = NAN;
                }
                else
                {
                    workingStack[workingStackSize++] = number1/number2;
                }
            } break;

            case Token_Circumflex:
            {
                if(workingStackSize < 2)
                {
                    parsed->failed = 1;
                    break;
                }

                double number2 = workingStack[--workingStackSize];
                double number1 = workingStack[--workingStackSize];
                workingStack[workingStackSize++] = pow(number1, number2);
            } break;

            default:
            {
                if(nextToken->type == Token_Function)
                {
                    MathFunction function = getFunctionFromToken(*nextToken);
                    if(workingStackSize < 1 || function == Function_Unknown)
                    {
                        char name[1<<10];
                        tokenTextToBuffer(*nextToken, name);
                        printf("Identifier %s with %d arguments is not defined.\n",
                               name, workingStackSize);
                        parsed->failed = 1;
                    }
                    else
                    {
                        switch(function)
                        {
                            case Function_Sin:
                            {
                                workingStack[workingStackSize-1] = sin(workingStack[workingStackSize-1]);
                            } break;

                            case Function_Cos:
                            {
                                workingStack[workingStackSize-1] = cos(workingStack[workingStackSize-1]);
                            } break;

                            case Function_Tan:
                            {
                                workingStack[workingStackSize-1] = tan(workingStack[workingStackSize-1]);
                            } break;

                            case Function_Sqrt:
                            {
                                if(workingStack[workingStackSize-1] >= 0.0)
                                {
                                    workingStack[workingStackSize-1] = sqrt(workingStack[workingStackSize-1]);
                                }
                                else
                                {
                                    workingStack[workingStackSize-1] = NAN;
                                }
                            } break;

                            case Function_Ln:
                            {
                                if(workingStack[workingStackSize-1] > 0.0)
                                {
                                    workingStack[workingStackSize-1] = log(workingStack[workingStackSize-1]);
                                }
                                else
                                {
                                    workingStack[workingStackSize-1] = NAN;
                                }
                            } break;

                            case Function_Log:
                            {
                                if(workingStackSize < 2)
                                {
                                    char name[1<<10];
                                    tokenTextToBuffer(*nextToken, name);
                                    printf("Identifier %s with %d arguments is not defined.\n",
                                           name, workingStackSize);
                                    parsed->failed = 1;
                                }
                                else
                                {
                                    double number2 = workingStack[--workingStackSize];
                                    double number1 = workingStack[--workingStackSize];
                                    if(number1 > 0 && number2 > 0 && number1 != 1)
                                    {
                                        workingStack[workingStackSize++] = log(number2)/log(number1);
                                    }
                                    else
                                    {
                                        workingStack[workingStackSize++] = NAN;
                                    }
                                }
                            } break;

                            case Function_Abs:
                            {
                                workingStack[workingStackSize-1] = fabs(workingStack[workingStackSize-1]);
                            } break;

                            default: {} break;
                        }
                    }
                }
                else
                {
                    printf("Unknown token found while evaluating!\n");
                }
            } break;
        }
    }

    if(parsed->failed)
    {
        char buffer[1<<8] = {0};
        memcpy(buffer, parsed->text, parsed->textLength);
        printf("Malformed expression: %s\n", buffer);
        return NAN;
    }

    if(workingStackSize != 1)
    {
        return NAN;
    }
    else
    {
        return *workingStack;
    }
}

void drawLine(Image *image, int x0, int y1, int x1, int y2, uint8_t r, uint8_t g, uint8_t b)
{
    bool steep = 0;
    if(abs(x0 - x1) < abs(y1-y2))
    {
        steep = 1;

        int temp = x0;
        x0 = y1;
        y1 = temp;

        temp = x1;
        x1 = y2;
        y2 = temp;
    }

    if(x0 > x1)
    {
        int temp = x0;
        x0 = x1;
        x1 = temp;

        temp = y1;
        y1 = y2;
        y2 = temp;
    }

    int deltaX = x1-x0;
    int deltaY = y2-y1;
    int error2 = 0;
    int deltaError2 = abs(deltaY)*2;

    int y = y1;
    for(int x = x0; x <= x1; ++x)
    {
        if(!steep && x >= 0 && x < image->width && y >= 0 && y < image->height)
        {
            uint8_t *pixel = image->data + y*image->stride + x*3;
            pixel[0] = r;
            pixel[1] = g;
            pixel[2] = b;
        }
        else if(steep && y >= 0 && y < image->width && x >= 0 && x < image->height)
        {
            uint8_t *pixel = image->data + x*image->stride + y*3;
            pixel[0] = r;
            pixel[1] = g;
            pixel[2] = b;
        }

        error2 += deltaError2;
        if(error2 > deltaX)
        {
            y += (y2>y1) ? 1 : -1;
            error2 -= deltaX*2;
        }
    }
}

int getPosOnImage(double target, double min, double max, int dimension, bool inverse)
{
    double value = ((target-min) / (max-min)) * (dimension-1);
    if(inverse)
    {
        value = (dimension-1) - value;
    }

    int result = (int)round(value);

    return result;
}

bool pointIsVisible(Image *image, int x, int y)
{
    return (x >= 0 && x < image->width &&
            y >= 0 && y < image->height);
}

bool pointIsInThreshold(Image *image, int x, int y)
{
    int threshold = 10000;
    return (x >= -threshold && x < image->width + threshold &&
            y >= -threshold && y < image->height + threshold);
}

void getNextGraphColor(int *colorIndex)
{
    int numberOfColors = arrayCount(graphColors);
    ++(*colorIndex);
    if(*colorIndex < 0 || *colorIndex >= numberOfColors)
    {
        *colorIndex = 0;
    }
}

int main()
{
    ExpressionParserOptions options = {0};

    for(;;)
    {
        if(options.shouldExit)
        {
            return 0;
        }

        char buffer[1<<12] = {0};
        printf("Insert the expression: ");
        fgets(buffer, arrayCount(buffer), stdin);

        ExpressionsResult result = {0};
        if(!parseExpression(buffer, &result, &options))
        {
            continue;
        }

        if(options.graphing)
        {
            Image image;
            image.width = 1025;
            image.height = 769;
            image.stride = 3*image.width;
            image.data = (uint8_t *)calloc(image.height*image.stride, 1);

            // NOTE(nox): Draw axes
            int originX = getPosOnImage(0, options.minX, options.maxX, image.width, 0);
            int originY = getPosOnImage(0, options.minY, options.maxY, image.height, 1);
            drawLine(&image, 0, originY, image.width-1, originY, 255, 255, 255);
            drawLine(&image, originX, 0, originX, image.height-1, 255, 255, 255);

            double increment = (options.maxX - options.minX) / (image.width - 1);

            int colorIndex = -1;
            for(int i = 0; i < result.numExpressions; ++i)
            {
                int *colors;
                if(!options.definedGraphColor)
                {
                    getNextGraphColor(&colorIndex);
                    colors = graphColors[colorIndex];
                }
                else
                {
                    colors = options.graphColor;
                }

                double prevX = NAN, prevY = NAN;
                int x0 = 0, y0 = 0;

                for(options.variableValue = options.minX; options.variableValue <= options.maxX;
                    options.variableValue += increment)
                {
                    double x = options.variableValue;
                    double y = solveParsedExpression(result.queues+i, &options);

                    // NOTE(nox): Hole filler
                    if(prevY == prevY && y != y && prevX == prevX)
                    {
                        double temp = options.variableValue;

                        options.variableValue = prevX;
                        double goal = options.variableValue + increment;
                        while(options.variableValue < goal)
                        {
                            options.variableValue += increment*0.001;
                            double newY = solveParsedExpression(result.queues+i, &options);
                            if(newY != newY)
                            {
                                break;
                            }
                            else
                            {
                                x = options.variableValue;
                                y = newY;
                            }
                        }

                        options.variableValue = temp;
                    }
                    if(prevY != prevY && y == y && prevX == prevX)
                    {
                        double temp = options.variableValue;

                        double goal = prevX;
                        while(options.variableValue > goal)
                        {
                            options.variableValue -= increment*0.001;
                            double newY = solveParsedExpression(result.queues+i, &options);
                            if(newY != newY)
                            {
                                break;
                            }
                            else
                            {
                                prevX = options.variableValue;
                                prevY = newY;
                            }
                        }

                        if(prevY == prevY)
                        {
                            x0 = getPosOnImage(prevX, options.minX, options.maxX,
                                                      image.width, 0);
                            y0 = getPosOnImage(prevY, options.minY, options.maxY,
                                                      image.height, 1);
                        }

                        options.variableValue = temp;
                    }

                    int x1 = getPosOnImage(x, options.minX, options.maxX, image.width, 0);
                    int y1 = getPosOnImage(y, options.minY, options.maxY, image.height, 1);

                    if(y == y && prevY == prevY &&
                       pointIsInThreshold(&image, x0, y0) && pointIsInThreshold(&image, x1, y1))
                    {
                        if(pointIsVisible(&image, x0, y0) || pointIsVisible(&image, x1, y1))
                        {
                            drawLine(&image, x0, y0, x1, y1, (uint8_t)colors[0], (uint8_t)colors[1],
                                     (uint8_t)colors[2]);
                        }

                        x0 = x1;
                        y0 = y1;
                    }
                    else if(y == y)
                    {
                        x0 = getPosOnImage(x, options.minX, options.maxX, image.width, 0);
                        y0 = getPosOnImage(y, options.minY, options.maxY, image.height, 1);
                    }

                    prevX = x;
                    prevY = y;
                }
            }

            stbi_write_png("output.png", image.width, image.height, 3, image.data, image.stride);
            free(image.data);
        }
        else
        {
            for(int i = 0; i < result.numExpressions; ++i)
            {
                printf("#%d Result: %lf\n", i+1, solveParsedExpression(result.queues+i, &options));
            }
        }
    }
}
